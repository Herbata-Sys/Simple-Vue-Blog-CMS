<template>
  <div class="deleteuser" :style="this[theme]">
    <h2><font-awesome-icon icon="times"/> Usuń użytkownika</h2>

    <div class="deleteuser__container">
      <div class="deleteuser__text">
        Na pewno chcesz usunąć użytkownika o id: <b>{{ $route.params.id }}</b>? Zostaną usunięte wszystkie komentarze, odpowiedzi i artykuły napisane przez użytkownika. Operacji nie da się cofnąć.
      </div>
      <input type="button" value="Usuń" class="deleteuser__button" @click="deleteUser">
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';

export default {
  name: 'deleteUser',

  data() {
    return {
      lightCss: {
        '--main-text-color': 'black',
        '--button-hover-color': '#fdd872',
      },
      darkCss: {
        '--main-text-color': '#d8d8d8',
        '--button-hover-color': '#ffb10030',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  methods: {
    ...mapActions([
      'showInfo',
    ]),

    deleteUser() {
      axios.get('/api/deleteUser.php', {
        params: {
          id: this.$route.params.id,
        },
      })
        .then((response) => {
          response = response.data;
          if (response.success) this.$router.push('/admin/users');
          this.showInfo(response.info);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.deleteuser{

  &__button{
    transition: background .5s;
    will-change: background;
    cursor: pointer;
    display: block;
    background: transparent;
    padding: 5px 10px;
    margin: 10px auto;
    border: 1px solid #ffbc00;
    font-weight: 600;
    font-size: 20px;
    color: var(--main-text-color);

    &:hover{
      background: var(--button-hover-color);
    }
  }

  &__text{
    text-align: center;
    font-size: 20px;
    padding: 40px 0 20px 0;
  }
}
</style>
