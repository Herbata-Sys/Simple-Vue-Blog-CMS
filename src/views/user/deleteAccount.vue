<template>
  <div class="deleteAccount" :style="this[theme]">
    <h2><font-awesome-icon icon="user-slash"/> Usuń konto</h2>

    <Loading v-if="loading" />

    <div v-if="!loading" class="deleteAccount__info">Zostaną usunięte wszystkie twoje komentarze i odpowiedzi, jest to nieodwracalne.</div>
    <vue-recaptcha v-if="captchaKey" ref="recaptcha" class="login__captcha" @verify="onCaptchaVerified" :sitekey="captchaKey" :theme="theme === 'lightCss' ? 'light' : 'dark'" @expired="onCaptchaExpired"></vue-recaptcha>
    <input type="submit" @click="deleteAccount" class="deleteAccount__submit" value="Usuń swoje konto">
  </div>
</template>

<script>
import { mapMutations, mapActions } from 'vuex';
import VueRecaptcha from 'vue-recaptcha';
import axios from 'axios';
import Loading from '@/components/Loading.vue';

export default {
  name: 'deleteAccount',

  components: {
    VueRecaptcha,
    Loading,
  },

  data() {
    return {
      loading: false,
      captchaKey: '',
      recaptchaToken: null,
      lightCss: {
        '--main-text-color': 'black',
        '--button-hover-color': '#fdd872',
      },
      darkCss: {
        '--main-text-color': '#d8d8d8',
        '--button-hover-color': '#ffb10030',
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
    ]),

    ...mapActions([
      'showInfo',
    ]),

    deleteAccount() {
      this.loading = true;
      if (this.recaptchaToken !== null) {
        const formdata = new FormData();
        formdata.append('captcha', this.recaptchaToken);
        const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

        axios
          .post('/api/deleteAccount.php', formdata, config)
          .then((response) => {
            response = response.data;
            this.showInfo(response.info);

            const user = {
              name: '',
              logged: '',
            };
            this.set_User(user);
            this.loading = false;
            this.$router.push({ path: '/' });
          });
      } else this.showInfo('Najpierw potwierdź, że nie jesteś robotem');
    },

    onCaptchaVerified(recaptchaToken) {
      this.recaptchaToken = recaptchaToken;
    },

    onCaptchaExpired() {
      this.$refs.recaptcha.reset();
    },
  },

  mounted() {
    this.captchaKey = JSON.parse(localStorage.blog).captcha;
  },
};
</script>

<style lang="scss" scoped>
.deleteAccount{
  &__info{
    margin: 10px 0;
  }

  &__submit{
    transition: background .5s;
    will-change: background;
    cursor: pointer;
    display: block;
    background: transparent;
    padding: 5px 10px;
    margin: 10px 0;
    border: 1px solid #ffbc00;
    font-weight: 600;
    font-size: 20px;
    color: var(--main-text-color);

    &:hover{
      background: var(--button-hover-color);
    }
  }
}
</style>
