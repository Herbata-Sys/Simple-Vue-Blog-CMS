<template>
  <div :style="this[theme]" id="app">
    <div v-show="!loading" class="container">
      <Header ref="header" :class="{'header--animate': menuAnimation}" />

      <router-view class="router-view"/>

      <Footer />
    </div>

    <transition name="info">
      <Info v-show="$store.state.infoShow" />
    </transition>
  </div>
</template>

<script>
import { mapMutations } from 'vuex';
import axios from 'axios';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import Info from '@/components/Info.vue';

export default {
  name: 'App',

  components: {
    Header,
    Footer,
    Info,
  },

  data() {
    return {
      menuAnimation: 0,
      loading: 1,
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
          this.loading -= 1;
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

    menuSlide() {
      if (this.$refs.header.$el.scrollHeight - window.scrollY <= 0) this.menuAnimation = 1;
      else this.menuAnimation = 0;
    },
  },

  created() {
    this.themeStorage();
    window.addEventListener('scroll', this.menuSlide);
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

.header--animate{
  animation: slide-in-top 1s linear both;
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
  animation: slide-in-elliptic-top-fwd 0.7s linear;
  will-change: opacity;
}

.router-view{
  min-height: 110vh;
}

.info-enter-active {
  animation: bounce-in-top 1s;
}

.info-leave-active {
  animation: slide-out-bottom 2s cubic-bezier(0.550, 0.085, 0.680, 0.530) both;
}

@keyframes slide-in-elliptic-top-fwd {
  0% {
    transform: translateY(-600px) rotateX(-30deg) scale(0);
    transform-origin: 50% 100%;
    opacity: 0;
  }
  100% {
    transform: translateY(0) rotateX(0) scale(1);
    transform-origin: 50% 1400px;
    opacity: 1;
  }
}

@keyframes slide-in-top {
  0% {
    position: fixed;
    transform: translateY(-200px);
    opacity: 0;
  }
  100% {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    transform: translateY(0) translateX(0);
    opacity: 1;
    width: 100%;
    background: var(--background-color);
  }
}

@keyframes bounce-in-top {
  0% {
    transform: translateY(-500px) translateX(-50%);
    animation-timing-function: ease-in;
    opacity: 0;
  }
  38% {
    transform: translateY(0) translateX(-50%);
    animation-timing-function: ease-out;
    opacity: 1;
  }
  55% {
    transform: translateY(-65px) translateX(-50%);
    animation-timing-function: ease-in;
  }
  72% {
    transform: translateY(0) translateX(-50%);
    animation-timing-function: ease-out;
  }
  81% {
    transform: translateY(-28px) translateX(-50%);
    animation-timing-function: ease-in;
  }
  90% {
    transform: translateY(0) translateX(-50%);
    animation-timing-function: ease-out;
  }
  95% {
    transform: translateY(-8px) translateX(-50%);
    animation-timing-function: ease-in;
  }
  100% {
    transform: translateY(0) translateX(-50%);
    animation-timing-function: ease-out;
  }
}

@keyframes slide-out-bottom {
  0% {
    transform: translateY(0) translateX(-50%);
    opacity: 1;
  }
  100% {
    transform: translateY(300px) translateX(-50%);
    opacity: 0;
  }
}

</style>
