<template>
  <div class="blog" :style="this[theme]">
    <h2><font-awesome-icon icon="columns"/> Ogólne ustawienia bloga</h2>

    <form class="password__form" v-on:submit.prevent="updateBlog">
      <div class="blog__info">Logo <b>(50px x 50px)</b> format: <b>.png</b>:</div>
      <input type="file" accept="image/png, .png" ref="file" v-on:change="onChangeFileUpload" required>
      <div class="blog__logo">
        <img v-if="url" :src="url" />
      </div>

      <div class="blog__info">Tytuł bloga, który widnieje przy logo, maksymalnie 50 znaków:</div>
      <input type="text" class="blog__input" placeholder="Tytuł bloga" v-model="blog.title" maxlength="50" minlength="3" required>

      <div class="blog__info">Podtytuł bloga, znajduje się przy tytule, maksymalnie 50 znaków:</div>
      <input type="text" class="blog__input" placeholder="Podtytuł bloga" v-model="blog.subtitle" maxlength="50" minlength="3" required>

      <div class="blog__info">Klucz captcha <strong>dla witryny</strong>:</div>
      <input type="text" class="blog__input" placeholder="Captcha - klucz witryny" v-model="blog.captcha" required>
      <input type="submit" class="blog__submit" value="Zapisz">
    </form>
  </div>
</template>

<script>
import { mapActions, mapMutations } from 'vuex';
import axios from 'axios';

export default {
  name: 'blog',

  data() {
    return {
      url: '/img/logo.png',
      file: '',
      blog: {},
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
      'set_Blog',
    ]),

    ...mapActions([
      'showInfo',
    ]),

    updateBlog() {
      const formdata = new FormData();
      formdata.append('title', this.blog.title);
      formdata.append('subtitle', this.blog.subtitle);
      formdata.append('captcha', this.blog.captcha);
      formdata.append('file', this.$refs.file.files.length > 0 ? this.file : 'empty');
      const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

      axios
        .post('/api/updateBlog.php', formdata, config)
        .then((response) => {
          response = response.data;
          if (response.success) {
            this.set_Blog(this.blog);
          }
          this.showInfo(response.info);
        });
    },

    onChangeFileUpload() {
      [this.file] = this.$refs.file.files;
      this.url = URL.createObjectURL(this.file);
    },
  },

  mounted() {
    this.blog = JSON.parse(localStorage.blog);
  },
};
</script>

<style lang="scss" scoped>
.blog{

  &__form{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 30px 0;
  }

  &__input{
    display: block;
    background: transparent;
    padding: 10px 5px;
    font-size: 18px;
    margin: 0px 0 10px 0;
    width: 100%;
    border: 3px dashed #ffbc00;
    border-top: none;
    color: var(--main-text-color);
    border-bottom-left-radius: 10px;
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

  &__info{
    margin-top: 20px;
    padding: 10px;
    font-size: 17px;
    background: #ffbc00;
    color: black;
    border-top-right-radius: 10px;
  }

  &__logo img{
    width: 50px;
    height: 50px;
    margin: 10px;
    border: 3px dashed rgb(255, 196, 3);
  }
}
</style>
