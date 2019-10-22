<template>
  <div class="tags" :style="this[theme]">
    <div class="tags__searchBox">
      <input type="text" class="tags__search" v-model="search" required>
      <div class="tags__placeholder">Wpisz Tag</div>
    </div>

    <TagsShow v-if="!loading" :tags="tags" :search="search" />
  </div>
</template>

<script>
import axios from 'axios';
import TagsShow from '@/components/TagsShow.vue';

export default {
  name: 'tags',

  components: {
    TagsShow,
  },

  data() {
    return {
      loading: true,
      search: '',
      tags: [{ id: 0, name: '' }],
      lightCss: {
        '--main-text-color': 'black',
      },
      darkCss: {
        '--main-text-color': 'white',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  methods: {
    getTags() {
      axios.get('/api/getTags.php')
        .then((response) => {
          this.tags = response.data.data;
          this.loading = false;
        });
    },
  },

  mounted() {
    this.getTags();
    document.title = 'Wyszukaj tag';
  },
};
</script>

<style lang="scss" scoped>
.tags{

  &__searchBox{
    position: relative;
    margin: 40px auto 100px auto;
    max-width: 200px;
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
