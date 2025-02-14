<template>
  <component
    :is="componentIs"
    class="listview-base-item"
    :class="{ 'is-no-border': noBorder }"
    v-bind="href ? { href } : {}"
    :target="newtab ? '_blank' : undefined"
    :rel="newtab ? 'noopener' : undefined"
    :type="submit ? 'submit' : undefined"
    @click="onClick"
  >
    <slot />
  </component>
</template>

<script>
export default {
  props: {
    href: {
      type: String,
      default: null
    },
    newtab: {
      type: Boolean,
      default: false
    },
    noBorder: {
      type: Boolean,
      default: false
    },
    button: {
      type: Boolean,
      default: false
    },
    submit: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    componentIs() {
      if (this.button) return 'button'
      return this.href ? 'a' : 'div'
    }
  },
  methods: {
    onClick(e) {
      this.$emit('click', e)
    }
  }
}
</script>

<style lang="scss" scoped>
.listview-base-item {
  $listview-border: 1px solid $color-border;

  --listview-base-item-padding-x: #{$spacing};
  @media screen and (max-width: $breakpoint-container-padding-reduce) {
    --listview-base-item-padding-x: #{$spacing-md};
  }

  background-color: transparent;
  border: 0;
  color: $color-text;
  cursor: pointer;
  display: block;
  margin: 0;
  outline-offset: -3px;
  padding: $spacing-s var(--listview-base-item-padding-x);
  position: relative;
  transition: #{$transition-base-fast} background-color;
  width: 100%;
  &:not(a):not(button) {
    cursor: auto;
  }
  &::after {
    border-bottom: $listview-border;
    bottom: 0;
    content: '';
    display: block;
    height: 1px;
    left: var(--listview-base-item-padding-x);
    position: absolute;
    right: 0;
  }
  &.is-no-border::after {
    display: none;
  }
  &:first-child {
    border-top-left-radius: $border-radius;
    border-top-right-radius: $border-radius;
  }
  &:last-child {
    border-bottom-left-radius: $border-radius;
    border-bottom-right-radius: $border-radius;
    &::after {
      display: none;
    }
  }
  &:hover,
  &:active,
  &:focus {
    background: $color-bg-light;
    color: $color-text;
    text-decoration: none;
  }
  &:not(a):not(button):hover,
  &:not(a):not(button):active,
  &:not(a):not(button):focus {
    background: $color-bg-surface;
  }
  &.is-action-btn {
    align-items: center;
    appearance: none;
    color: $color-primary;
    display: flex;
    flex-direction: column;
    font-weight: $font-bold;
    justify-content: center;
    padding-bottom: $spacing;
    padding-top: $spacing;
  }
  &__title {
    font-size: 1.1rem;
    font-weight: $font-bold;
    margin: 0;
  }
  &__meta {
    font-size: 1rem;
    margin: 0;
  }
  &__body {
    color: $color-muted;
    font-size: 1rem;
    margin: $spacing-xs 0 0;
  }
}
</style>
