<template>
  <div :style="this[theme]" class="siteMap">
      <router-link class="siteMap__link" to="/">
        Strona główna
      </router-link>

      <font-awesome-icon class="siteMap__arrow" icon="arrow-right"/>
      {{ location }}
  </div>
</template>

<script>
export default {
  name: 'SiteMap',

  props: {
    location: String,
  },

  data() {
    return {
      lightCss: {
        '--text-color': 'black',
      },
      darkCss: {
        '--text-color': '#d8d8d8',
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
.siteMap{
  margin: 20px 0;

  &__link{
    color: var(--text-color);
    text-decoration: none;
    font-weight: 600;
  }
}

</style>
