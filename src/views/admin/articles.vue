<template>
  <div class="articles" :style="this[theme]">
    <h2><font-awesome-icon icon="newspaper"/> Lista wszystkich artykułów</h2>

    <div v-if="!loading" class="articles__container">
      <table class="articles__table">
        <tr>
          <th @click="sortBy('id')" title="Sortuj po ID">ID</th>
          <th @click="sortBy('id')" title="Sortuj po dacie">Dodany</th>
          <th @click="sortBy('author')" title="Sortuj po Autorze">Autor</th>
          <th @click="sortBy('title')" title="Sortuj po Tytule">Tytuł</th>
          <th>Akcje</th>
        </tr>
        <tr v-for="article in articles" :key="article.id">
          <td><router-link :to="'/article/'+article.id" class="articles__id">#{{ article.id }}</router-link></td>
          <td class="articles__date">{{ article.date }}</td>
          <td>{{ article.author }}</td>
          <td><div class="articles__tableText">{{ article.title }}</div></td>
          <td><div><router-link :to="'editArticle/'+article.id" class="articles__edit">Edytuj</router-link></div><router-link :to="'deleteArticle/'+article.id" class="articles__delete">Usuń</router-link></td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'articles',

  data() {
    return {
      loading: 1,
      sorting: {
        id: 'desc',
      },
      articles: [],
      lightCss: {
        '--main-text-color': 'black',
        '--tr-background': '#f2f2f2',
        '--td-border': '#e7e7e7',
        '--tr-hover': '#d4d6ff',
      },
      darkCss: {
        '--main-text-color': '#d8d8d8',
        '--tr-background': '#f2f2f212',
        '--td-border': '#525252',
        '--tr-hover': '#00000047',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  methods: {
    getArticles() {
      axios.get('/api/articlesGetAll.php')
        .then((response) => {
          this.articles = response.data.data;
          this.loading = 0;
        });
    },

    sortBy(by) {
      if (this.sorting[by] === 'desc') {
        this.articles.sort((a, b) => {
          if (by === 'id') {
            a[by] = parseInt(a[by], 10);
            b[by] = parseInt(b[by], 10);
          }

          a[by] = a[by];
          b[by] = b[by];
          if (a[by] < b[by]) return -1;
          if (a[by] > b[by]) return 1;
          return 0;
        });
        this.sorting[by] = 'asc';
      } else {
        this.articles.sort((a, b) => {
          if (by === 'id') {
            a[by] = parseInt(a[by], 10);
            b[by] = parseInt(b[by], 10);
          }

          a[by] = a[by];
          b[by] = b[by];
          if (a[by] > b[by]) return -1;
          if (a[by] < b[by]) return 1;
          return 0;
        });
        this.sorting[by] = 'desc';
      }
    },
  },

  mounted() {
    this.getArticles();
  },
};
</script>

<style lang="scss" scoped>
.articles{

  &__container{
    animation: rolldown .7s;
    overflow: hidden;
  }

  &__table{
    margin: 20px 0;
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;

    a{
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: 1s transform;

      &:hover{
        transform: translateY(-3px)
      }
    }

    th, td {
      text-align: left;
      white-space: pre;
      padding: 15px 10px;
      vertical-align: top;
      text-align: center;
    }

    tr:nth-child(even){
      background-color: var(--tr-background);
      box-shadow: 1px 1px 20px -10px black;

      td{
        border-right: 1px solid var(--td-border);
        border-left: 1px solid var(--td-border);
      }
    }

    tr:not(:first-child){
      transition: .3s background-color;
      will-change: background-color;

      &:hover{
        background-color: var(--tr-hover);
      }
    }

    tr:first-child{

      th:first-child{
        border-top-left-radius: 10px;
      }

      th:last-child{
        border-top-right-radius: 10px;
      }
    }

    th{
      text-align: center;
      color: white;
      background: #213477;
      cursor: pointer;
    }

    tr td:nth-of-type(4){
      white-space: unset;
      word-break: break-word;
      text-overflow: ellipsis;
    }

  }

  &__tableText{
    max-height: 200px;
    overflow-y: auto;
  }

  &__edit{
    color: #0087ff;
  }

  &__delete{
    color: #ff0000;
  }

  &__id{
    color: #ff6100;
  }

  &__date{
    font-size: 12px;
  }
}

@keyframes rolldown {
  0% {
    height: 0;
    opacity: 0;
    transform: scaleY(0);
    transform-origin: 0 0
  }
  100% {
    height: 500px;
    opacity: 1;
    transform: scaleY(1);
    transform-origin: 0 0
  }
}
</style>
