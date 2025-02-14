<template>
  <div class="grid">
    <template v-if="paginator">
      <div class="grid-toolbar" v-if="$slots.toolbar">
        <slot name="toolbar" />
      </div>
      <div class="grid-controls">
        <button
          class="btn is-transparent is-no-border"
          v-tooltip="'最初のページ'"
          :disabled="loading || page === 1"
          @click="onClickFirst"
        >
          <i class="fas fa-angle-double-left fa-fw"></i>
        </button>
        <button
          class="btn is-transparent is-no-border"
          v-tooltip="'前のページ'"
          :disabled="loading || page === 1"
          @click="onClickPrev"
        >
          <i class="fas fa-chevron-left fa-fw"></i>
        </button>
        <button
          class="btn is-transparent is-no-border"
          v-tooltip="'次のページ'"
          :disabled="loading || page === paginator.last_page"
          @click="onClickNext"
        >
          <i class="fas fa-chevron-right fa-fw"></i>
        </button>
        <button
          class="btn is-transparent is-no-border"
          v-tooltip="'最後のページ'"
          :disabled="loading || page === paginator.last_page"
          @click="onClickLast"
        >
          <i class="fas fa-angle-double-right fa-fw"></i>
        </button>
        <button
          class="btn is-transparent is-no-border"
          v-tooltip="'再読み込み'"
          :disabled="loading"
          @click="onClickReload"
        >
          <i class="fas fa-sync fa-fw"></i>
        </button>
        <div class="grid-controls__section is-no-padding">
          <button
            class="btn is-transparent is-no-border"
            @click="onClickFilter"
          >
            <i class="fas fa-filter fa-fw"></i>
            絞り込み
            <i
              class="fas fa-circle grid-controls__notifier"
              v-if="isFilterActive"
            ></i>
          </button>
        </div>
        <div class="grid-controls__section is-no-padding">
          <AppDropdown
            :items="[10, 25, 50, 100, 250, 500]"
            name="grid-per-page"
          >
            <template #button="{ toggle, props }">
              <button
                class="btn is-transparent is-no-border"
                :disabled="loading"
                @click="toggle"
                v-bind="props"
              >
                表示件数 :&nbsp;
                {{ perPage }}&nbsp;
                <i class="fas fa-caret-down"></i>
              </button>
            </template>
            <template #item="{ item }">
              <AppDropdownItem
                class="grid-controls__selector-item"
                component-is="button"
                @click="(e) => onChangePerPage(item, e)"
              >
                {{ item }}
                <i
                  class="fas fa-check grid-controls__selector-item__icon"
                  v-if="perPage === item"
                ></i>
              </AppDropdownItem>
            </template>
          </AppDropdown>
        </div>
        <div class="grid-controls__section">
          <template v-if="paginator.total > 0">
            {{ paginator.from }}〜{{ paginator.to }}件目 • 全{{
              paginator.total
            }}件 (ページ{{ paginator.current_page }} /
            {{ paginator.last_page }})
          </template>
          <template v-else>0件</template>
        </div>
        <div class="grid-controls__section text-primary" v-if="loading">
          <i class="fas fa-spinner fa-pulse"></i>
        </div>
      </div>
      <div class="grid__table_wrap">
        <table class="grid-table">
          <thead class="grid-table__thead">
            <tr class="grid-table__tr">
              <th class="grid-table__th"></th>
              <th class="grid-table__th" v-for="keyName in keys" :key="keyName">
                <button
                  class="grid-table__th__button"
                  :disabled="!sortableKeys.includes(keyName)"
                  @click="(e) => onClickTh(keyName, e)"
                >
                  <slot name="th" :keyName="keyName" />
                  <template v-if="orderBy === keyName">
                    <i
                      v-if="direction === 'asc'"
                      class="fas fa-fw fa-sort-up text-primary"
                    ></i>
                    <i v-else class="fas fa-fw fa-sort-down text-primary"></i>
                  </template>
                  <i
                    v-else-if="sortableKeys.includes(keyName)"
                    class="fas fa-fw fa-sort text-muted"
                  ></i>
                </button>
              </th>
            </tr>
          </thead>
          <tbody class="grid-table__tbody">
            <tr
              class="grid-table__tr is-in-tbody"
              v-for="row in paginator.data"
              :key="row.id"
            >
              <td class="grid-table__td">
                <slot name="activities" :row="row" />
              </td>
              <td
                class="grid-table__td"
                v-for="keyName in keys"
                :key="`${row.id}-${keyName}`"
              >
                <slot name="td" :row="row" :keyName="keyName" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
    <div class="grid-loading" v-else>
      <i class="fas fa-spinner fa-pulse fa-2x text-primary"></i>
    </div>
  </div>
</template>

<script>
import AppDropdown from './AppDropdown.vue'
import AppDropdownItem from './AppDropdownItem.vue'

export default {
  components: {
    AppDropdown,
    AppDropdownItem
  },
  props: {
    keys: {
      type: Array,
      required: true
    },
    sortableKeys: {
      type: Array,
      required: true
    },
    orderBy: {
      type: String,
      required: true
    },
    direction: {
      type: String,
      required: true
    },
    paginator: {
      type: Object,
      required: false
    },
    page: {
      type: Number,
      required: true
    },
    perPage: {
      type: Number,
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    },
    isFilterActive: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    onClickFirst(e) {
      this.$emit('clickFirst', e)
    },
    onClickPrev(e) {
      this.$emit('clickPrev', e)
    },
    onClickNext(e) {
      this.$emit('clickNext', e)
    },
    onClickLast(e) {
      this.$emit('clickLast', e)
    },
    onClickReload(e) {
      this.$emit('clickReload', e)
    },
    onClickFilter(e) {
      this.$emit('clickFilter', e)
    },
    onClickTh(keyName, e) {
      this.$emit('clickTh', keyName, e)
    },
    onChangePerPage(perPage, e) {
      this.$emit('changePerPage', perPage, e)
    }
  }
}
</script>

<style lang="scss" scoped>
.grid {
  &-toolbar {
    display: flex;
    flex-wrap: wrap;
    padding: $spacing-md $spacing-sm;
  }
  &-controls {
    align-items: center;
    background: $color-bg-base;
    display: flex;
    flex-wrap: wrap;
    font-size: 0.9rem;
    padding: $spacing-sm;
    &__section {
      display: inline-block;
      padding: 0 $spacing-md;
      position: relative;
      &.is-no-padding {
        padding: 0;
      }
      &::before {
        border-left: 1px solid $color-border;
        content: '';
        display: block;
        height: 1.5rem;
        left: 0;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
      }
    }
    &__selector-item {
      align-items: center;
      display: flex;
      min-width: 7.5rem;
      &__icon {
        color: $color-primary;
        margin-left: auto;
      }
    }
    &__notifier {
      color: $color-primary;
      transform: scale(0.5);
    }
  }
  &-loading {
    align-items: center;
    display: flex;
    flex-direction: column;
    height: 300px;
    justify-content: center;
    width: 100%;
  }
  &__table_wrap {
    background: $color-bg-surface;
    border-bottom: 1px solid $color-border;
    border-top: 1px solid $color-border;
    overflow: auto;
    width: 100%;
  }
  &-table {
    border: 0;
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;
    &__thead {
      border-bottom: 1px solid $color-border;
    }
    &__th {
      padding: 0;
      &__button {
        appearance: none;
        background: transparent;
        border: none;
        color: $color-text;
        cursor: pointer;
        display: block;
        font-size: 0.9rem;
        font-weight: $font-bold;
        padding: $spacing $spacing-md;
        text-align: left;
        white-space: nowrap;
        width: 100%;
        &:disabled {
          cursor: auto;
        }
      }
    }
    &__tr {
      border: 0;
      &.is-in-tbody {
        &:nth-child(2n) {
          background: $color-grid-table-stripe;
        }
        &:hover {
          background: $color-bg-light;
        }
      }
    }
    &__td {
      font-size: 0.9rem;
      padding: $spacing-sm $spacing-md;
      white-space: nowrap;
    }
  }
}
</style>
