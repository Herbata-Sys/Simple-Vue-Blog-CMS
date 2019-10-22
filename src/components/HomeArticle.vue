<template>
  <article :style="this[theme]" class="article">
    <div class="article__content">
      <div class="article__background" v-bind:style="{ backgroundImage: 'url(/img/articles/thumb/'+article.image+')' }"></div>

      <router-link class="article__title" title="Czytaj dalej" :to="{ name: 'article', params: { id: article.id }}">
        {{ article.title }}
        <div class="article__admin" v-if="this.$store.state.user.admin==='1'">
          <router-link class="article__edit" :to="'/admin/editArticle/'+article.id" title="Edytuj artykuł">[Edytuj] </router-link>
          <router-link class="article__delete" :to="'/admin/deleteArticle/'+article.id" title="Usuń artykuł">[Usuń]</router-link>
        </div>
      </router-link>

      <div class="article__info">
        <div class="article__author">{{ article.author }}</div>
        <div class="article__date">{{ article.date }}</div>
      </div>

      <router-link title="Czytaj dalej" :to="{ name: 'article', params: { id: article.id }}">
        <font-awesome-icon class="article__readMore" icon="chevron-right"/>
      </router-link>

      <p class="article__text">
        {{ article.shortText | cutText }}
      </p>

      <div class="article__tags tags">
          <ul class="tags__list">
            <router-link v-for="tag in article.tags" :key="tag.id" class="tags__link" title="Zobacz tag" :to="{ name: 'tag', params: { id: tag.id }}">
              <li class="tags__item"># {{ tag.name }}</li>
            </router-link>
          </ul>
      </div>
    </div>
  </article>
</template>

<script>
export default {
  name: 'HomeArticle',

  props: {
    article: Object,
  },

  data() {
    return {
      lightCss: {
        '--main-text-color': 'white',
        '--article-content-background': '#00000098',
        '--author-color': 'rgb(223, 223, 223)',
        '--main-yellow': '#ffbb20',
        '--tags-black': 'black',
        '--article-box-shadow': '#c3c3c333',
        '--paragraph-text-background': '#ffffff14',
      },
      darkCss: {
        '--main-text-color': 'white',
        '--article-content-background': '#000000d9',
        '--author-color': 'rgb(223, 223, 223)',
        '--main-yellow': '#ffbb20',
        '--tags-black': 'black',
        '--article-box-shadow': '#c3c3c333',
        '--paragraph-text-background': '#ffffff14',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  filters: {
    cutText(value) {
      value = value.toString();
      return value.substring(0, 500);
    },
  },
};
</script>

<style scoped lang="scss">
.article{
  overflow: hidden;
  align-self: flex-start;
  color: var(--main-text-color);
  width: 80%;
  margin: auto;
  position: relative;
  margin-bottom: 50px;
  transition: width .7s;
  will-change: transform, width;
  box-shadow: 0px 0px 1px var(--article-box-shadow);

  &__content{
    padding: 30px;
    background: var(--article-content-background);

    &:hover .article__background{
      transform: scale(1.2);
    }
  }

  &__admin{
    font-size: 15px;
    font-weight: 900;
    display: inline-block;
    text-shadow: 0px 1px 1px #000000ad;
  }

  &__edit{
    border-radius: 10px;
    padding: 5px;
    background: #31313185;
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
    background: #31313185;
    color: rgb(255, 73, 73);
    text-decoration: none;
    display: inline-block;
    transition: 1s transform;

    &:hover{
      transform: translateY(-3px)
    }
  }

  &__title{
    font-size: 30px;
    font-weight: 600;
    cursor: pointer;
    color: var(--main-text-color);
    text-decoration: none;
  }

  &__info{
    margin-bottom: 20px;
    margin-top: 10px;
    font-weight: 600;
    font-style: italic;
    font-size: 12px;
  }

  &__author{
    display: inline-block;
    color: var(--author-color);
  }

  &__date{
    display: inline-block;
    margin-left: 10px;
  }

  &__text{
    position: relative;
    font-size: 14px;
    text-align: justify;
    background: var(--paragraph-text-background);
    padding: 10px;
    font-weight: 400;
    border-radius: 5px;
    line-height: 20px;
    word-break: break-all;

    &::before{
      content: '';
      position: absolute;
      width: 70%;
      height: calc(100% + 30px);
      border-bottom: 1px solid var(--main-yellow);
    }
  }

  &__background{
    top: 0;
    left: 0;
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: none;
    background-size: cover;
    background-position: center;
    z-index: -1;
    filter: blur(3px);
    transition: all 2s;
  }

  &__readMore{
    position: absolute;
    bottom: 30px;
    right: 30px;
    font-size: 30px;
    color: rgba(255, 255, 255, 0.658);
    cursor: pointer;
    transition: transform 2s;
    will-change: transform;

    &:hover{
      transform: translateX(10px) rotate(-180deg);
    }
  }
}

.tags{

  &__list{
    display: inline-block;
    margin-top: 40px;
    font-size: 11px;
  }

  &__item{
    border-radius: 3px;
    padding: 3px 5px;
    display: inline-block;
    margin: 10px;
    cursor: pointer;
    font-weight: 600;
    color: var(--main-text-color);
    background-color: var(--tags-black);
    transition: background-color 0.5s, color 0.5s;
    will-change: background-color, color;

    &:hover{
      color: var(--tags-black);
      background: var(--main-text-color);
    }
  }
}

@media only screen and (max-width: 1100px) {
  .article{
    width: 100%;

    &__content{
      padding: 20px;
    }
  }
}
</style>
