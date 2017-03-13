<template>
  <div class="spin-wrapper">
    <slot></slot>
    <div class="spin">
      <Spin size="large" fix v-if="loading"></Spin>
    </div>
  </div>
</template>

<script>
export default {
  name: 'spin-wrapper',
  props: ['loading'],
  data () {
    return {}
  }
}
</script>

<style scoped>
.spin-wrapper {
  position: relative;
}

.spin {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
</style>

