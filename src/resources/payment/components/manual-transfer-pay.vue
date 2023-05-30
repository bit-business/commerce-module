<template>
  <div @click="emit">
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    order: Object
  },
  methods: {
    emit() {
      this.$emit("input", this.redirect);
    },
    redirect(res) {
      this.$inertia.visit(this.route('skijasi.commerce-theme.payment-info', this.order.id))
    },
  },
}
</script>