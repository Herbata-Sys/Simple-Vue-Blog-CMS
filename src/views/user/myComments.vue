<template>
  <div class="comments" :style="this[theme]">
    <h2><font-awesome-icon icon="comment-alt"/> Moje komentarze</h2>
    <p>
      <Comments v-if="!loading" :comments="comments" :viewOnly="true"/>
    </p>
  </div>
</template>

<script>
import axios from 'axios';
import Comments from '@/components/Comments.vue';

export default {
  name: 'myComments',

  components: {
    Comments,
  },

  data() {
    return {
      loading: true,
      comments: [],
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
    getComments() {
      axios.get('/api/myComments.php')
        .then((response) => {
          this.comments = response.data.data;
          this.loading = false;
        });
    },
  },

  mounted() {
    this.getComments();
  },
};
</script>

<style lang="scss" scoped>
</style>
