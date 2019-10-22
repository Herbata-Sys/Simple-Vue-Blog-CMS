<template>
  <div :style="this[theme]" class="article">
    <div v-show="!loading">
      <div class="article__image">
        <img :src="'/img/articles/' + article.image" @load="loading -= 1">
      </div>

      <div class="article__content">
        <SiteMap :location="article.title" />

        <div class="article__title">
          {{ article.title }}
          <div class="article__admin" v-if="this.$store.state.user.admin==='1'">
            <router-link class="article__edit" :to="'/admin/editArticle/'+article.id" title="Edytuj artykuł">[Edytuj] </router-link>
            <router-link class="article__delete" :to="'/admin/deleteArticle/'+article.id" title="Usuń artykuł">[Usuń]</router-link>
          </div>
        </div>

        <div class="article__info">
          <div class="article__author">
            {{ article.author }}
          </div>

          <div class="article__date">
            {{ article.date }}
          </div>
        </div>

        <pre class="article__text" v-html="article.text"></pre>

        <ArticleTags :tags="article.tags" />

        <SiteMap class="article--dashedBorder" :location="article.title" />

        <Comments :comments="comments" />

      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import SiteMap from '@/components/SiteMap.vue';
import Comments from '@/components/Comments.vue';
import ArticleTags from '@/components/ArticleTags.vue';

export default {
  name: 'ArticleShow',

  components: {
    SiteMap,
    Comments,
    ArticleTags,
  },

  data() {
    return {
      loading: 2,
      article: {
        id: 0,
        title: '',
        image: 'none.jpg',
        shortText: '',
      },
      comments: [],
      lightCss: {
        '--article-background-color': 'white',
        '--text-color': 'black',
        '--comments-title-background': '#fdd872',
      },
      darkCss: {
        '--article-background-color': '#1d1d1d',
        '--text-color': '#d8d8d8',
        '--comments-title-background': '#fdd872',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  methods: {
    getArticle() {
      axios.get('/api/getArticle.php', {
        params: { id: this.$route.params.id },
      })
        .then((response) => {
          this.article = response.data.data;
          document.title = this.article.title;
          this.loading -= 1;
        });
    },

    getComments() {
      axios.get('/api/getComments.php', {
        params: { article_id: this.$route.params.id },
      })
        .then((response) => {
          this.comments = response.data.data;
        });
    },
  },

  mounted() {
    this.getArticle();
    this.getComments();
  },
};
</script>

<style scoped lang="scss">
.article{
  color: var(--text-color);

  &__image{
    animation: slide-in-top 0.7s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;

    img{
      display: block;
      width: 100%;
      margin: auto;
      height: auto;
      z-index: -1;
      min-height: 200px;
    }
  }

  &__content{
    animation: bounce-in-bottom 1.1s both;
    min-height: 700px;
    position: relative;
    margin: auto;
    margin-top: -100px;
    width: 90%;
    background-color: var(--article-background-color);
    padding: 10px 20px;
    word-break: break-word;
  }

  &__title{
    font-size: 50px;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--text-color);
    font-weight: 600;
    font-size:40px;
  }

  &__admin{
    font-size: 15px;
    font-weight: 600;
    display: inline-block;
  }

  &__edit{
    border-radius: 10px;
    padding: 5px;
    background: #def3ff85;
    color: rgb(56, 119, 255);
    text-decoration: none;
    display: inline-block;
    transition: 1s transform;
    margin-right: 5px;

    &:hover{
      transform: translateY(-3px)
    }
  }

  &__delete{
    border-radius: 10px;
    padding: 5px;
    background: #ffdede85;
    color: rgb(255, 73, 73);
    text-decoration: none;
    display: inline-block;
    transition: 1s transform;

    &:hover{
      transform: translateY(-3px)
    }
  }

  &__author{
    font-weight: 600;
  }

  &__text{
    padding-top: 30px;
    font-size: 20px;
  }

  &--dashedBorder{
    padding-bottom: 40px;
    border-bottom: 1px dashed var(--text-color);
  }

  &__link{
    color: var(--text-color);
    text-decoration: none;
    font-weight: 600;
  }

  @keyframes bounce-in-bottom {
    0% {
      -webkit-transform: translateY(500px);
              transform: translateY(500px);
      -webkit-animation-timing-function: ease-in;
              animation-timing-function: ease-in;
      opacity: 0;
    }
    38% {
      -webkit-transform: translateY(0);
              transform: translateY(0);
      -webkit-animation-timing-function: ease-out;
              animation-timing-function: ease-out;
      opacity: 1;
    }
    55% {
      -webkit-transform: translateY(65px);
              transform: translateY(65px);
      -webkit-animation-timing-function: ease-in;
              animation-timing-function: ease-in;
    }
    72% {
      -webkit-transform: translateY(0);
              transform: translateY(0);
      -webkit-animation-timing-function: ease-out;
              animation-timing-function: ease-out;
    }
    81% {
      -webkit-transform: translateY(28px);
              transform: translateY(28px);
      -webkit-animation-timing-function: ease-in;
              animation-timing-function: ease-in;
    }
    90% {
      -webkit-transform: translateY(0);
              transform: translateY(0);
      -webkit-animation-timing-function: ease-out;
              animation-timing-function: ease-out;
    }
    95% {
      -webkit-transform: translateY(8px);
              transform: translateY(8px);
      -webkit-animation-timing-function: ease-in;
              animation-timing-function: ease-in;
    }
    100% {
      -webkit-transform: translateY(0);
              transform: translateY(0);
      -webkit-animation-timing-function: ease-out;
              animation-timing-function: ease-out;
    }
  }

  @keyframes slide-in-top {
    0% {
      -webkit-transform: translateY(-1000px);
              transform: translateY(-1000px);
      opacity: 0;
    }
    100% {
      -webkit-transform: translateY(0);
              transform: translateY(0);
      opacity: 1;
    }
  }
}

@media only screen and (max-width: 800px) {
  .article{

    &__content{
      width: 100%;
      padding: 10px 5px;
      margin-top: 0;
      border-top: 2px solid #ffbc00;

    }

    &__image img{
      border-top-right-radius: 20px;
      border-top-left-radius: 20px;
    }

    &__text{
      padding-top: 0;
    }
  }
}

@media only screen and (max-width: 400px) {
  .article{

    &__image{
      width: 114%;
      margin-left: -7%;

      img{
        border-radius: 0;
      }
    }

    &__text{
      padding-top: 0;
    }
  }
}
</style>

<style scoped>
.article__text >>> p{
  margin: 40px 0;
  font-size: 20px;
}
</style>
