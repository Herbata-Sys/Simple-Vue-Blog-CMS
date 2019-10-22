<template>
  <div class="home" :style="this[theme]">
    <div class="articles" v-if="!loading">
      <div class="articles__view">
        <label class="articles__label">
          <input @click="viewChange($event)" class="articles__radio" v-model="picked" type="radio" name="view" value="list">
          <font-awesome-icon class="articles__subradio" title="Widok listy" icon="stream"/>
        </label>
        <label class="articles__label">
          <input @click="viewChange($event)" class="articles__radio" v-model="picked" type="radio" name="view" value="tiles">
          <font-awesome-icon class="articles__subradio" title="Widok kafelkowy" icon="th-large"/>
        </label>
      </div>
      <div class="articles__content" :class='{ "articles--grid": picked=="tiles" }'>
        <HomeArticle v-for="article in articles" :key="article.id" :article="article"/>
      </div>

      <div class="articles__load" title="Załaduj kolejne 5 artykułów" @click="getArticles">
        Więcej
      </div>
    </div>
  </div>
</template>

<script>
import { mapMutations } from 'vuex';
import axios from 'axios';
import HomeArticle from '@/components/HomeArticle.vue';

export default {
  name: 'home',

  components: {
    HomeArticle,
  },

  data() {
    return {
      loading: true,
      picked: 'list',
      lightCss: {
        '--button-background-color': '#ffb100',
        '--button-text-color': 'white',
        '--button-unactive-text-color': 'black',
        '--main-text-color': 'black',
      },
      darkCss: {
        '--button-background-color': '#ffb100',
        '--button-text-color': 'black',
        '--button-unactive-text-color': 'white',
        '--main-text-color': '#d8d8d8',
      },
    };
  },

  methods: {
    ...mapMutations([
      'push_Articles',
      'set_Page',
      'set_Articles',
    ]),

    viewChange(e) {
      localStorage.view = e.target.value;
    },

    getArticles() {
      axios.get('/api/articlesGetFive.php', {
        params: { page: (this.$store.state.page + 1) },
      })
        .then((response) => {
          this.push_Articles(response.data.data);
          this.set_Page(this.$store.state.page + 1);
        });
    },

    getFiveArticles() {
      axios.get('/api/articlesGetFive.php', {
        params: { page: 1 },
      })
        .then((response) => {
          this.set_Articles(response.data.data);
          this.loading = false;
        });
    },
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },

    blog() {
      return this.$store.state.blog;
    },

    articles() {
      return this.$store.state.articles;
    },
  },

  mounted() {
    if (localStorage.view) {
      this.picked = localStorage.view;
    } else {
      localStorage.view = this.picked;
    }

    this.getFiveArticles();
    document.title = `${JSON.parse(localStorage.blog).title} - ${JSON.parse(localStorage.blog).subtitle}`;
  },
};
</script>

<style lang="scss">

.articles{
  &--grid{
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-column-gap: 40px;
    grid-row-gap: 40px;
    margin-bottom: 50px;

    .article{
      width: 100%;
      align-self: stretch;
      margin: 0;

      &__content{
        padding: 30px;
        height: 100%;
      }
    }
  }

  &__load{
    color: var(--main-text-color);
    text-align: center;
    margin: 40px auto;
    cursor: pointer;
    font-weight: 600;
    font-size: 20px;
    transition: transform .5s;
    will-change: transform;
    width: fit-content;
    border-bottom: 2px dashed #ffbb00;

    &:hover{
      transform: scale(1.2);
    }
  }

  &__view{
    display: flex;
    justify-content: flex-end;
  }

  &__radio{
    opacity: 0;
    position: absolute;
    left: 0;
    top: 0;

    &:checked ~ .articles__subradio{
      background-color: var(--button-background-color);
      color: var(--button-text-color);
    }
  }

  &__subradio{
    margin-left: 10px;
    font-size: 40px;
    padding: 10px;
    cursor: pointer;
    margin-bottom: 30px;
    transition: color 1s, background-color 1s;
    will-change: color, background-color;
    color: var(--button-unactive-text-color);
  }

  @media only screen and (max-width: 800px) {
    .articles{

      &__view{
        display: none;
      }
    }
  }
}
</style>
