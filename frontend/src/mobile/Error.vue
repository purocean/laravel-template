<template>
  <div class="error">
    <h1>错误 {{ $route.query.code }}</h1>
    <p> {{ $route.query.message }} </p>
  </div>
</template>

<script>
export default {
  name: 'error',
  data () {
    return {
    }
  },
}
</script>

<style scoped>
.error {
  text-align: center;
}
</style>
