<template>
  <div class="deletearticle" :style="this[theme]">
    <h2><font-awesome-icon icon="times"/> Usuń artykuł</h2>

    <div class="deletearticle__container" v-if="!loading">
      <div class="deletearticle__text">
        Na pewno chcesz usunąć artykuł o id: <b>{{ article.id }}</b> i tytule: <b>"{{ article.title }}"</b>? Zostaną usunięte także wszystkie komentarze i odpowiedzi powiązane z artykułem. Operacji nie da się cofnąć.
      </div>
      <input type="button" value="Usuń" class="deletearticle__button" @click="deleteArticle">
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';

export default {
  name: 'deleteArticle',

  data() {
    return {
      loading: 1,
      article: {},
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

    getArticle() {
      axios.get('/api/getArticle.php', {
        params: {
          id: this.$route.params.id,
        },
      })
        .then((response) => {
          this.article = response.data.data;
          this.pickedTags = response.data.data.tags;
          this.loading = 0;
        });
    },

    deleteArticle() {
      axios.get('/api/deleteArticle.php', {
        params: {
          id: this.$route.params.id,
        },
      })
        .then((response) => {
          response = response.data;
          if (response.success) this.$router.push('/admin/articles');
          this.showInfo(response.info);
        });
    },
  },

  mounted() {
    this.getArticle();
  },
};
</script>

<style lang="scss" scoped>
.deletearticle{

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
