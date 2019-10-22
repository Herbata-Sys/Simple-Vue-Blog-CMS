<template>
  <section :style="this[theme]" class="tags">
    <div class="tags__title">
      Tagi
    </div>

    <ul class="tags__list">
      <router-link v-for="tag in tags" :key="tag.id" class="tags__link" title="Zobacz tag" :to="{ name: 'tag', params: { id: tag.id }}">
        <li class="tags__item">
          <img class="tags__icon" src="/img/tag.svg">
          <div class="tags__name">
            {{ tag.name }}
          </div>
        </li>
      </router-link>
    </ul>
  </section>
</template>

<script>
export default {
  name: 'ArticleTags',

  props: {
    tags: Array,
  },

  data() {
    return {
      lightCss: {
        '--article-background-color': 'white',
      },
      darkCss: {
        '--article-background-color': '#1d1d1d',
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

<style scoped lang="scss">
.tags{
  margin-bottom: 50px;
  margin-top: 30px;

  &__link{
    color: var(--main-text-color);
    text-decoration: none;
    text-transform: lowercase;
  }

  &__title{
    font-size: 30px;
    font-weight: 900;
    padding-bottom: 10px;
  }

  &__list{
    display: flex;
    flex-wrap: wrap;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  &__item{
    display: flex;
    padding-right: 10px;
    align-items: center;
    cursor: pointer;
    transition: transform 1s;
    will-change: transform;

    &:hover .tags__icon{
      transform: rotate(90deg);
    }

    &:hover{
      transform: translateY(5px);
    }
  }

  &__icon{
    width: 30px;
    transition: transform 1s;
    will-change: transform;
  }

  &__name{
    display: inline;
    font-weight: 600;
  }
}
</style>
