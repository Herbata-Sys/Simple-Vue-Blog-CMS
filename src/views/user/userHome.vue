<template :style="this[theme]">
  <div class="home">
    <h2><font-awesome-icon class="menu__icon" icon="user"/> Panel użytkownika</h2>
    <p>Z tego miejsca możesz zarządzać swoim kontem - zmienić hasło lub przejrzeć komentarze które utworzyłeś.</p>
    <p>Przejdź do zakładki która cię interesuje. Znajdziesz je w menu po lewej stronie.</p>
  </div>
</template>

<script>

export default {
  name: 'userHome',

  data() {
    return {
      lightCss: {
        '--main-text-color': 'black',
      },
      darkCss: {
        '--main-text-color': '#d8d8d8',
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

<style lang="scss" scoped>
</style>
