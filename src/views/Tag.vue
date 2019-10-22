<template>
  <div class="tag" :style="this[theme]">
    <div class="tag__name">
      #{{ tag.name }}
    </div>

    <div v-if="!articles.length" class="tag__none">Nie ma żadnych artykułów pod tym tagiem</div>

    <div class="articles__content" v-if="!loading">
      <HomeArticle v-for="article in articles" :key="article.id" :article="article"/>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import HomeArticle from '@/components/HomeArticle.vue';

export default {
  name: 'tag',

  components: {
    HomeArticle,
  },

  data() {
    return {
      loading: true,
      tag: [],
      articles: [
        {
          id: 0,
          title: '',
          image: 'none.jpg',
          shortText: '',
        },
      ],
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
    getTagInfo() {
      axios.get('/api/getTagInfo.php', {
        params: { id: this.$route.params.id },
      })
        .then((response) => {
          this.tag = response.data.data;
          document.title = `#${this.tag.name}`;
        });
    },

    getTagArticles() {
      axios.get('/api/getTagArticles.php', {
        params: { tag: this.$route.params.id },
      })
        .then((response) => {
          this.articles = response.data.data;
          this.loading = false;
        });
    },
  },

  mounted() {
    this.getTagInfo();
    this.getTagArticles();
    document.title = 'Wyszukaj tag';
  },

  watch: {
    $route() {
      this.getTagInfo();
      this.getTagArticles();
    },
  },
};
</script>

<style lang="scss" scoped>
.tag{

  &__name{
    font-size: 40px;
    text-align: center;
    margin: 20px 0 100px 0;
    font-weight: 900;
    color: var(--main-text-color);
  }

  &__none{
    font-size: 40px;
    text-align: center;
    margin: 20px 0 100px 0;
    color: var(--main-text-color);
  }
}
</style>
