<?php

namespace App\Http\Controllers\Staff\Circles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eloquents\Circle;
use App\Eloquents\User;
use App\Http\Requests\Staff\Circles\CircleRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\Circles\CirclesService;
use App\Services\Circles\Exceptions\DenyCreateTagsException;
use Illuminate\Support\Facades\DB;

class UpdateAction extends Controller
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var CirclesService
     */
    private $circlesService;

    public function __construct(User $user, CirclesService $circlesService)
    {
        $this->user = $user;
        $this->circlesService = $circlesService;
    }

    public function __invoke(Circle $circle, CircleRequest $request)
    {
        if (!$circle->hasSubmitted()) {
            // 参加登録が未提出の企画の情報は閲覧・編集できない
            abort(404);
        }

        DB::beginTransaction();

        $member_ids = str_replace(["\r\n", "\r", "\n"], "\n", $request->members);
        $member_ids = explode("\n", $member_ids);
        $member_ids = array_unique(array_filter($member_ids, "strlen"));

        $leader = $this->user->firstByStudentId($request->leader);
        if (!empty($leader)) {
            $member_ids = array_diff($member_ids, [$leader->student_id]);
        }
        $members = $this->user->getByStudentIdIn($member_ids);

        $status_changed = false;
        $status = $circle->status;
        $status_set_at = $circle->status_set_at;
        $status_set_by = $circle->status_set_by;

        if ($request->status === Circle::STATUS_PENDING) {
            $status = null;
            $status_set_at = null;
            $status_set_by = null;
        } elseif (
            in_array($request->status, [Circle::STATUS_APPROVED, Circle::STATUS_REJECTED], true) &&
            $request->status !== $circle->status
        ) {
            $status_changed = true;
            $status = $request->status;
            $status_set_at = now();
            $status_set_by = Auth::id();
        }

        // 保存処理
        $circle->update([
            'name'  => $request->name,
            'name_yomi'  => $request->name_yomi,
            'group_name'  => $request->group_name,
            'group_name_yomi'  => $request->group_name_yomi,
            'status' => $status,
            'status_reason' => $request->status_reason,
            'status_set_at' => $status_set_at,
            'status_set_by' => $status_set_by,
            'notes' => $request->notes
        ]);
        $circle->users()->detach();

        if (!empty($leader)) {
            $leader->circles()->attach($circle->id, ['is_leader' => true]);
        }
        foreach ($members as $member) {
            $member->circles()->attach($circle->id, ['is_leader' => false]);
        }
        $circle->save();

        // 場所の保存
        $this->circlesService->savePlaces($circle, $request->places ?? [], Auth::user());

        // タグの保存
        try {
            $this->circlesService->saveTags(
                $circle,
                $request->tags ?? [],
                Auth::user()->can('staff.tags.edit'),
                Auth::user()
            );
        } catch (DenyCreateTagsException $e) {
            DB::rollBack();
            return redirect()
                ->route('staff.circles.edit', $circle)
                ->withInput()
                ->withErrors(['tags' => $e->getMessage()]);
        }

        if ($status_changed === true) {
            $circle->load('users');
            if ($circle->status === Circle::STATUS_APPROVED) {
                foreach ($circle->users as $user) {
                    $this->circlesService->sendApprovedEmail($user, $circle);
                }
            } elseif ($circle->status === Circle::STATUS_REJECTED) {
                foreach ($circle->users as $user) {
                    $this->circlesService->sendRejectedEmail($user, $circle);
                }
            }
        }

        DB::commit();

        return redirect()
            ->back()
            ->with('topAlert.title', '企画情報を更新しました');
    }
}
