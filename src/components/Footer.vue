<template>
  <footer :style="this[theme]" class="footer">
    {{ blog.title }}, wszystkie prawa zastrzeżone. 2019
  </footer>
</template>

<script>
export default {
  name: 'Footer',

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

    blog() {
      return this.$store.state.blog;
    },
  },
};
</script>

<style scoped lang="scss">
.footer{
  color: var(--main-text-color);
  min-height: 100px;
  border-top: 2px solid #ffbc00;
  padding: 30px;
  text-align: center;
  font-weight: 400;
}
</style>
