@extends('layouts.no_drawer')

@section('no_circle_selector', true)

@section('title', 'パスワードの再設定')

@section('content')
    <form method="POST">
        @csrf

        <app-container medium>
            <list-view>
                <template v-slot:title>パスワードの再設定</template>
                <list-view-card>
                    <p>まず、「{{ config('app.name') }}」にログインするために使用していた{{ config('portal.student_id_name') }}または連絡先メールアドレスを入力してください。
                    </p>
                    <p>連絡先メールアドレスに対し、パスワード再設定に関するご案内を差し上げます。</p>
                </list-view-card>
                <list-view-form-group label-for="login_id">
                    <template v-slot:label>{{ config('portal.student_id_name') }}または連絡先メールアドレス</template>
                    <input id="login_id" type="text" class="form-control @error('login_id') is-invalid @enderror"
                        name="login_id" value="{{ auth()->user()->email ?? old('login_id') }}" required autofocus />
                    @error('login_id')
                        <template v-slot:invalid>{{ $message }}</template>
                    @enderror
                </list-view-form-group>
            </list-view>
        </app-container>
        <app-container class="text-center pt-spacing-md">
            <button type="submit" class="btn is-primary is-wide">次へ</button>
        </app-container>
    </form>
@endsection
