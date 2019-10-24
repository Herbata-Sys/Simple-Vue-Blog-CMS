<template>
  <header :style="this[theme]">
    <font-awesome-icon @click="mobileMenuExpand=!mobileMenuExpand" class="menu__hamburger menu__link" icon="bars"/>

    <nav>
      <router-link class="logo" to="/">
        <img class="logo__image" src="/img/logo.png">

        <span class="logo__text">
          {{ blog.title }}
          <span class="logo__subtext"> {{ blog.subtitle }}</span>
        </span>
      </router-link>

      <ul :class="menuMobileClass" class="menu">
        <li class="menu__item">
          <router-link to="/search">
            <font-awesome-icon title="Szukaj" class="menu__link menu--search" icon="search"/>
          </router-link>
        </li>

        <li class="menu__item">
          <font-awesome-icon @click="themeChange" title="Zmień styl strony" class="menu__link menu--theme" icon="adjust"/>
        </li>

        <li class="menu__item">
          <router-link class="menu__link menu--normal" to="/">Strona główna</router-link>
        </li>

        <li class="menu__item">
          <router-link class="menu__link menu--normal" to="/tags">Tagi</router-link>
        </li>

        <li v-if="$store.state.user.logged" class="menu__item">
          <router-link class="menu__link menu--login" title="Zarządzanie kontem" to="/user">
            <font-awesome-icon v-if="$store.state.user.admin == 1" title="Konto administratora" class="menu__link menu--userIco" icon="user-tie"/>
            <font-awesome-icon v-if="$store.state.user.admin == 0" title="Konto użytkownika" class="menu__link menu--userIco" icon="user"/>
            {{ $store.state.user.name }}
          </router-link>
        </li>

        <li v-if="$store.state.user.logged" class="menu__item">
          <font-awesome-icon @click="logout" title="Wyloguj" class="menu__link menu--theme" icon="sign-out-alt"/>
        </li>

        <li v-if="!$store.state.user.logged" class="menu__item">
          <div @click="login_Expand();registerExpanded=false;mobileMenuExpand=false" class="menu__link menu--login">Logowanie</div>
        </li>
      </ul>
    </nav>

    <section :class="{ 'login--visible' : loginExpanded }" class="login">
      <div class="login__container">
        <form v-on:submit.prevent="login" class="login__form">
          <div class="login__title">Logowanie</div>
          <input class="login__input" type="email" placeholder="email" minlength="4" v-model="loginData.email" required>
          <input class="login__input" type="password" placeholder="hasło" minlength="4" v-model="loginData.password" required>
          <vue-recaptcha v-if="captchaKey" ref="recaptcha1" class="login__captcha" @verify="onCaptchaVerified" :sitekey="captchaKey" :theme="theme === 'lightCss' ? 'light' : 'dark'" @expired="onCaptchaExpired1"></vue-recaptcha>
          <input class="login__submit" type="submit" value="Zaloguj">
        </form>

        <div class="login__register">Nie masz konta? <span @click="loginRegister" class="login__link">Zarejestruj się</span></div>

        <font-awesome-icon @click="login_Expand" title="Zamknij" class="login__close" icon="window-close"/>
      </div>
    </section>

    <section :class="{ 'register--visible' : registerExpanded }" class="register">
      <div class="login__container">
        <form v-on:submit.prevent="register" class="register__form">
          <div class="register__title">Rejestracja</div>
          <input class="register__input" type="text" v-model="registerData.name" placeholder="nazwa użytkownika" minlength="3" maxlength="20" required>
          <input class="register__input" type="email" v-model="registerData.email" placeholder="email" minlength="4" required>
          <input class="register__input" type="password" v-model="registerData.password" placeholder="hasło" minlength="6" required>
          <input class="register__input" type="password" v-model="registerData.passwordRepeat" placeholder="powtórz hasło" minlength="6" required>
          <vue-recaptcha v-if="captchaKey" ref="recaptcha2" class="login__captcha" @verify="onCaptchaVerified" :sitekey="captchaKey" :theme="theme === 'lightCss' ? 'light' : 'dark'" @expired="onCaptchaExpired2"></vue-recaptcha>
          <input class="register__submit" type="submit" value="Zarejestruj się">
        </form>

        <font-awesome-icon @click="registerExpanded=!registerExpanded" title="Zamknij" class="register__close" icon="window-close"/>
      </div>
    </section>

    <section class="cookies" v-if="!cookiesAccepted">
      <div class="cookies__text">
        Ten serwis korzysta z plików cookies. Przeglądając dalej zgadzasz się na zapisywanie i odczytywanie ciasteczek z twoje urządzenia.
      </div>
      <div class="cookies__button" @click="cookiesClose">
        Schowaj
      </div>
    </section>
  </header>
</template>

<script>
import { mapMutations, mapActions } from 'vuex';
import VueRecaptcha from 'vue-recaptcha';
import axios from 'axios';

export default {
  name: 'Header',

  components: {
    VueRecaptcha,
  },

  data() {
    return {
      recaptchaToken: null,
      mobileMenuExpand: false,
      captchaKey: '',
      cookiesAccepted: false,
      loginData: {
        email: '',
        password: '',
      },
      registerData: {
        name: '',
        email: '',
        password: '',
        passwordRepeat: '',
      },
      registerExpanded: false,
      lightCss: {
        '--main-text-color': 'black',
        '--menu-border-color': '#ffbc00',
        '--button-hover-color': '#fdd872',
        '--login-background-color': 'white',
        '--login-border-and-input-colors': '#ffb100',
        '--login-link-color': '#ff9a00',
        '--link-router-color': '#ff9f3f1c',
        '--hamburger-shadow': 'black',
      },
      darkCss: {
        '--main-text-color': 'white',
        '--menu-border-color': '#ffbc00',
        '--button-hover-color': '#ffb10030',
        '--login-background-color': '#101010',
        '--login-border-and-input-colors': '#ffb10030',
        '--login-link-color': '#ff9a00',
        '--link-router-color': '#ffffff1c',
        '--hamburger-shadow': 'white',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
    blog() {
      return this.$store.state.blog;
    },
    loginExpanded() {
      return this.$store.state.loginExpanded;
    },
    menuMobileClass() {
      return [this.mobileMenuExpand ? 'menu--visible' : ''];
    },
  },

  methods: {
    ...mapMutations([
      'login_Expand',
      'set_User',
    ]),

    ...mapActions([
      'showInfo',
    ]),

    loginRegister() {
      this.registerExpanded = !this.registerExpanded;
      this.login_Expand();
    },

    themeChange() {
      if (this.theme === 'lightCss') {
        this.$store.state.theme = 'darkCss';
        localStorage.theme = 'darkCss';
      } else {
        this.$store.state.theme = 'lightCss';
        localStorage.theme = 'lightCss';
      }
    },

    login() {
      if (this.recaptchaToken !== null) {
        const formdata = new FormData();
        formdata.append('email', this.loginData.email);
        formdata.append('password', this.loginData.password);
        formdata.append('captcha', this.recaptchaToken);
        const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

        axios
          .post('/api/login.php', formdata, config)
          .then((response) => {
            response = response.data;
            if (response.success === true) {
              this.login_Expand();
              response.data.logged = true;
              this.set_User(response.data);
            }
            this.showInfo(response.info);
            this.$refs.recaptcha1.reset();
            this.recaptchaToken = null;
          });
      } else this.showInfo('Najpierw potwierdź, że nie jesteś robotem');
    },

    register() {
      if (this.recaptchaToken !== null) {
        const formdata = new FormData();
        formdata.append('name', this.registerData.name);
        formdata.append('email', this.registerData.email);
        formdata.append('password', this.registerData.password);
        formdata.append('passwordRepeat', this.registerData.passwordRepeat);
        formdata.append('captcha', this.recaptchaToken);
        const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

        axios
          .post('/api/register.php', formdata, config)
          .then((response) => {
            response = response.data;
            if (response.success === true) {
              this.registerExpanded = false;
              this.login_Expand();
              this.registerData = {
                name: '',
                email: '',
                password: '',
                passwordRepeat: '',
              };
            }
            this.showInfo(response.info);
            this.$refs.recaptcha2.reset();
            this.recaptchaToken = null;
          });
      } else this.showInfo('Najpierw potwierdź, że nie jesteś robotem');
    },

    onCaptchaVerified(recaptchaToken) {
      this.recaptchaToken = recaptchaToken;
    },

    onCaptchaExpired1() {
      this.$refs.recaptcha1.reset();
    },

    onCaptchaExpired2() {
      this.$refs.recaptcha2.reset();
    },

    logout() {
      axios.get('/api/logout.php')
        .then((response) => {
          response = response.data;
          this.$router.push({ path: '/' });
          this.showInfo(response.info);
          const user = {
            name: '',
            logged: '',
          };
          this.set_User(user);
        });
    },

    cookiesClose() {
      this.cookiesAccepted = true;
      localStorage.setItem('cookiesAccepted', 'true');
    },

    checkCookies() {
      if (localStorage.getItem('cookiesAccepted') === 'true') this.cookiesAccepted = true;
    },
  },

  mounted() {
    this.captchaKey = JSON.parse(localStorage.blog).captcha;
    this.checkCookies();
  },
};
</script>

<style scoped lang="scss">
header{
  margin-bottom: 50px;
}

nav{
  display: grid;
  grid-template-columns: 400px auto;
  padding: 20px 10px;
}

.logo{
  align-self: center;
  justify-self: left;
  text-decoration: none;

  &__image{
    height: 50px;
    vertical-align: middle;
    animation: rotate 0.6s cubic-bezier(0.550, 0.085, 0.680, 0.530) both;
  }

  &__text{
    color: var(--main-text-color);
    font-size: 25px;
    font-weight: 200;
    padding-left: 20px;
    vertical-align: middle;
  }

  &__subtext{
    font-size: 13px;
    font-weight: 900;
    margin-left: 10px;
    position: relative;

    &::before{
      position: absolute;
      left: -5px;
      content: ".";
      top: 0;
      bottom: 0;
    }
  }
}


.menu{
  justify-self: right;
  align-self: center;
  list-style-type: none;
  padding: 0;

  &__hamburger{
    display: none;
  }

  &__item{
    display: inline-block;
    margin: 0 10px;
  }

  &__link {
    color: var(--main-text-color);
    text-decoration: none;
    text-transform: uppercase;
    font-size: 12px;
    font-weight: 900;
    &.router-link-exact-active {
      border-top: 2px solid var(--menu-border-color);
    }
  }

  &--normal{
    padding-top: 10px;
    transition: border-top 0.2s;

    &:hover{
      border-top: 2px solid var(--menu-border-color);
    }
  }

  &--login{
    border: 1px solid var(--menu-border-color);
    padding: 10px;
    transition: background-color 0.3s;
    will-change: background-color;
    cursor: pointer;

    &:hover{
      background-color: var(--button-hover-color);
    }
  }

  &--theme{
    cursor: pointer;
    transition: transform 0.3s;
    will-change: transform;

    &:hover{
      transform: scale(1.7) ;
    }
  }

  &--userIco{
    margin-right: 5px;
  }

  &--search{
    cursor: pointer;
    transition: transform 0.3s;
    will-change: transform;

    &:hover{
      transform: scale(1.7) ;
    }
  }
}

.login{
  visibility: hidden;
  opacity: 0;
  width: 100vw;
  height: 100vh;
  top: 0;
  left: 0;
  transform: scale(0);
  position: fixed;
  z-index: 9999;
  background-color: transparent;
  transition: opacity .5s, transform .5s, background-color 3s;
  will-change: opacity, transform, background-color,;


  &--visible{
    transform: scale(1);
    opacity: 1;
    visibility: visible;
    background-color: #0e100dd6;
  }

  &__container{
    color: var(--main-text-color);
    position: absolute;
    width: 500px;
    max-width: 95%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 10px 10px 30px 10px;
    border: 1px solid var(--login-border-and-input-colors);
    background: var(--login-background-color);
  }

  &__input{
    color: var(--main-text-color);
    font-size: 20px;
    padding: 5px 8px;
    background: var(--login-border-and-input-colors);
    border: none;
    display: block;
    margin: 10px auto;
    transition: transform .3s;
    will-change: transform;

    &::placeholder{
      color: var(--main-text-color);
    }

    &:focus{
      transform: scale3d(1.05, 1.05, 1.05);
    }
  }

  &__submit{
    background: none;
    border: 1px solid var(--login-border-and-input-colors);
    padding: 7px 15px;
    display: block;
    margin: 30px auto;
    font-size: 20px;
    cursor: pointer;
    font-weight: 600;
    color: var(--main-text-color);

    &:hover{
      background-color: var(--button-hover-color);
    }
  }

  &__title{
    font-size: 30px;
    font-weight: 200;
    margin-bottom: 30px;
    border-bottom: 1px solid var(--main-text-color);
  }

  &__close{
    position: absolute;
    right: 10px;
    top: 10px;
    font-size: 30px;
    cursor: pointer;
    background-color: none;
    color: var(--menu-border-color);
    transition: transform .3s;

    &:hover{
      transform: scale(1.15);
    }
  }

  &__register{
    text-align: center;
  }

  &__link{
    color: var(--main-text-color);
    font-weight: 600;
    cursor: pointer;
    position: relative;

    &::before{
      z-index: -1;
      top: -5px;
      position: absolute;
      width: 100%;
      height: 100%;
      content: '';
      border-bottom: 7px solid var(--login-link-color);
      transition: opacity 1s, transform 1s;
    }

    &:hover::before{
      opacity: 0;
      transform: translateX(-100%);
    }
  }

  &__captcha{
    display: flex;
    justify-content: center;
    transform: scale(0.85);
    max-width: 100vw;
  }
}

.register{
  visibility: hidden;
  opacity: 0;
  width: 100vw;
  height: 100vh;
  top: 0;
  left: 0;
  transform: scale(0);
  position: fixed;
  z-index: 9999;
  transition: opacity .5s, transform .5s, background-color 3s;
  will-change: opacity, transform;
  background-color: transparent;


  &--visible{
    transform: scale(1);
    opacity: 1;
    visibility: visible;
    background-color: #0e100dd6;
  }

  &__container{
    color: var(--main-text-color);
    position: absolute;
    width: 500px;
    max-width: 95%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 10px 10px 30px 10px;
    border: 1px solid var(--login-border-and-input-colors);
    background: var(--login-background-color);
  }

  &__input{
    color: var(--main-text-color);
    font-size: 20px;
    padding: 5px 8px;
    background: var(--login-border-and-input-colors);
    border: none;
    display: block;
    margin: 10px auto;
    transition: transform .3s;
    will-change: transform;

    &::placeholder{
      color: var(--main-text-color);
    }

    &:focus{
      transform: scale3d(1.05, 1.05, 1.05);
    }
  }

  &__submit{
    background: var(--login-background-color);
    border: 1px solid var(--login-border-and-input-colors);
    padding: 7px 15px;
    display: block;
    margin: 30px auto;
    font-size: 20px;
    cursor: pointer;
    font-weight: 600;
    color: var(--main-text-color);

    &:hover{
      background-color: var(--button-hover-color);
    }
  }

  &__title{
    font-size: 30px;
    font-weight: 200;
    margin-bottom: 30px;
    border-bottom: 1px solid var(--main-text-color);
  }

  &__close{
    position: absolute;
    right: 10px;
    top: 10px;
    font-size: 30px;
    cursor: pointer;
    background-color: var(--login-background-color);
    color: var(--menu-border-color);
    transition: transform .3s;

    &:hover{
      transform: scale(1.15);
    }
  }

  &__register{
    text-align: center;
  }

}

.cookies{
  position: fixed;
  bottom: 0;
  left: 0;
  padding: 20px;
  background: white;
  z-index: 9999;
  width: 100%;
  border: 1px solid #ffb100;
  text-align: center;

  &__text{
    font-size: 20px;
    display: inline-block;
  }

  &__button{
    display: inline-block;
    font-size: 20px;
    margin: 0 10px;
    color: #ffb100;
    cursor: pointer;
    font-weight: 900;
    border-bottom: 3px solid black;
  }
}

@keyframes rotate {
  0% {
    transform: rotate(360deg) scale(0);
    opacity: 0;
  }
  100% {
    transform: rotate(0) scale(1);
    opacity: 1;
  }
}

//widok mobilny
@media only screen and (max-width: 1000px) {
  header{
    margin-bottom: 10px;
  }

  nav{
    display: block;
    padding: 20px 10px;
  }

  .logo{

    &__text{
      position: relative;
      margin-top: -15px;
      display: inline-block;
    }

    &__subtext{
      position: absolute;
      bottom: -13px;
      left: 20px;
      width: 100%;
      display: block;
    }
  }

  .menu{
    display: none;
    transition: transform 2s;
    position: fixed;
    top: 0;
    right: 0;
    background: var(--login-background-color);
    width: 100%;
    text-align: center;
    height: 100%;
    z-index: 1100;
    border: 2px solid var(--menu-border-color);

    &__link{
      font-size: 30px;

      &.router-link-exact-active {
        border-top: 2px solid var(--menu-border-color);
        background-color: var(--link-router-color);
      }
    }

    &__item{
      display: block;
      padding: 20px;

      &:nth-child(-n+2){
        display: inline-block;
      }
    }

    &__hamburger {
      display: block;
      top: 20px;
      right: 5%;
      font-size: 40px;
      z-index: 1200;
      position: fixed;
      cursor: pointer;
      transition: transform 0.3s;
      will-change: transform;
      filter: drop-shadow( 3px 3px 2px var(--hamburger-shadow));

      &:active{
        transform: translateY(-10px);
      }
    }

    &--visible{
      display: block;
      animation: slide-in-blurred-right .5s cubic-bezier(0.230, 1.000, 0.320, 1.000) both;
    }
  }

  @keyframes slide-in-blurred-right {
    0% {
      transform: translateY(-500px)  scaleY(0.2);

      opacity: 0;
    }
    100% {
      transform: translateY(0) scaleY(1);
      opacity: 1;
    }
  }
}

</style>
