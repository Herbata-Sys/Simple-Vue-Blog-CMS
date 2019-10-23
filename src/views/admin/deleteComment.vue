<template>
  <div class="deletecomment" :style="this[theme]">
    <h2><font-awesome-icon icon="times"/> Usuń komentarz</h2>

    <div class="deletecomment__container">
      <div class="deletecomment__text">
        Na pewno chcesz usunąć komentarz o id: <b>{{ $route.params.id }}</b>? Zostaną usunięte również wszystkie odpowiedzi do komentarza. Operacji nie da się cofnąć.
      </div>
      <input type="button" value="Usuń" class="deletecomment__button" @click="deleteComment">
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';

export default {
  name: 'deleteComment',

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

    deleteComment() {
      axios.get('/api/deleteComment.php', {
        params: {
          id: this.$route.params.id,
        },
      })
        .then((response) => {
          response = response.data;
          if (response.success) {
            this.$router.push('/admin/users');
            this.$router.go(-2);
          }
          this.showInfo(response.info);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.deletecomment{

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
