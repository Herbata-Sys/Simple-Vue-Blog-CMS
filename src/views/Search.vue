<template>
  <div class="search" :style="this[theme]">
    <div class="search__searchBox">
      <input type="search" class="search__search" @input="search" required>
      <div class="search__placeholder">Czego szukasz?</div>
    </div>

    <div v-if="!articles || !articles.length" class="search__none">Nie ma żadnych artykułów pasujących do frazy</div>

    <div class="articles__content">
      <HomeArticle v-for="article in articles" :key="article.id" :article="article"/>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import _ from 'lodash.debounce';
import HomeArticle from '@/components/HomeArticle.vue';

export default {
  name: 'search',

  components: {
    HomeArticle,
  },

  data() {
    return {
      articles: [],
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

  methods: {
    search(e) {
      axios.get('/api/searchArticles.php', {
        params: { search: e.target.value },
      })
        .then((response) => {
          this.articles = response.data.data;
        });
    },
  },

  created() {
    this.search = _(this.search, 500);
  },

  mounted() {
    document.title = 'Wyszukaj artykuł';
  },
};
</script>

<style lang="scss" scoped>
.search{

  &__searchBox{
    position: relative;
    margin: 40px auto 100px auto;
    max-width: 300px;
    width: 95%;
  }

  &__search{
    color: var(--main-text-color);
    width: 100%;
    font-size: 40px;
    border: none;
    border-bottom: 3px solid #ffbc00;
    cursor: pointer;
    background: none;

    &:focus{
      border-bottom: 3px solid #ff774f;
    }
  }

  &__placeholder{
    position: absolute;
    pointer-events: none;
    left: 0;
    font-size: 40px;
    top: 0;
    transition: 0.2s ease all;
    color: var(--main-text-color);
    will-change: transform;
    animation: float 3s ease-in-out infinite;
  }

  &__searchBox input:focus ~ &__placeholder, &__searchBox input:not(:focus):valid ~ &__placeholder{
    top: -20px;
    left: 10px;
    font-size: 16px;
    opacity: 1;
  }

  &__none{
    font-size: 40px;
    text-align: center;
    margin: 20px 0 100px 0;
    color: var(--main-text-color);
  }
}

@keyframes float {
  0% {
    transform: translatey(-5px);
  }
  50% {
    transform: translatey(-10px);
  }
  100% {
    transform: translatey(-5px);
  }
}
</style>
