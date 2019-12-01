<template>
  <div class="loading__text">Ładowanie...</div>
</template>

<script>
export default {
  name: 'Loading',

  data() {
    return {
      lightCss: {
        '--main-text-color': 'black',
      },
      darkCss: {
        '--main-text-color': 'white',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },
};
</script>

<style scoped lang="scss">
.loading{
  color: var(--main-text-color);

  &__text{
    text-align: center;
    font-size: 30px;
    padding: 10px 0;
  }
}
</style>
