<template>
  <div :style="this[theme]" id="app">
    <div class="container">
      <Header />

      <router-view class="router-view"/>

      <Footer />
    </div>
  </div>
</template>

<script>
import { mapMutations } from 'vuex';
import axios from 'axios';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';

export default {
  name: 'App',

  components: {
    Header,
    Footer,
  },

  data() {
    return {
      blog: {
        title: '',
        subtitle: '',
      },
      lightCss: {
        '--background-color': 'white',
      },
      darkCss: {
        '--background-color': '#101010',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  methods: {
    ...mapMutations([
      'set_User',
      'set_Blog',
    ]),

    themeStorage() {
      if (localStorage.theme) {
        this.$store.state.theme = localStorage.theme;
      } else {
        localStorage.theme = this.theme;
      }
    },

    loadUser() {
      if (localStorage.user !== undefined) {
        this.$store.state.user = JSON.parse(localStorage.user);
      } else {
        localStorage.user = JSON.stringify(this.$store.state.user);
      }
    },

    loadBlogInfo() {
      axios.get('/api/blogInfo.php')
        .then((response) => {
          this.set_Blog(response.data.data);
        });
    },

    isLogged() {
      axios.get('/api/isLogged.php')
        .then((response) => {
          const user = {
            name: '',
            logged: '',
          };
          if (response.data.success === false) this.set_User(user);
        });
    },
  },

  created() {
    this.themeStorage();
  },

  mounted() {
    this.loadBlogInfo();
    this.loadUser();
    this.isLogged();
    document.title = `${JSON.parse(localStorage.blog).title} - ${JSON.parse(localStorage.blog).subtitle}`;
  },
};
</script>

<style lang="scss">
*{
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  -webkit-tap-highlight-color: transparent;
}

input:focus, textarea:focus, select:focus, button:focus{
  outline: none;
}

::selection {
  background: #ffc897;
}

#app{
  background-color: var(--background-color);
  font-family: 'Raleway', 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  overflow-y: hidden;
  min-height: 100vh;
}

pre{
  font-family: 'Raleway', 'Avenir', Helvetica, Arial, sans-serif;
  white-space: pre-wrap;
}

textarea{
  font-family: 'Raleway', 'Avenir', Helvetica, Arial, sans-serif;
}

.container{
  margin: auto;
  width: 90%;
  max-width: 1100px;
}

.router-view{
  min-height: 110vh;
}

</style>
