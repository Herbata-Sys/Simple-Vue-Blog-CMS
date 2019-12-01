<template>
  <div class="avatar" :style="this[theme]">
    <h2><font-awesome-icon icon="file-image"/> Zmień avatar</h2>

    <Loading v-if="loading" />

    <div v-if="!loading">
      <div class="avatar__info">Twój aktualny avatar:</div>
      <img v-if='loaded' :src='userAvatar == "" ? "/img/user.svg" : userAvatar' class="avatar__now">

      <form class="avatar__form" v-on:submit.prevent="changeAvatar">
        <div class="avatar__info">Twój avatar (rozszerzenie <b>.jpg</b> lub <b>.png</b>, optymalne wymiary <b>50px x 50px</b>):<input type="file" accept="image/jpeg,.jpg,.jpeg,.png" ref="file" v-on:change="onChangeFileUpload" required></div>

        <div class="avatar__preview">
          <img v-if="url" :src="url" />
        </div>
        <input type="submit" class="avatar__submit" value="Zapisz">
      </form>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';
import Loading from '@/components/Loading.vue';

export default {
  name: 'changeAvatar',

  components: {
    Loading,
  },

  data() {
    return {
      loading: false,
      loaded: false,
      url: null,
      file: '',
      userAvatar: '',
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
    ...mapActions([
      'showInfo',
    ]),

    changeAvatar() {
      this.loading = true;
      const formdata = new FormData();
      formdata.append('file', this.file);
      const config = { headers: { 'Content-Type': 'multipart/form-data' } };

      axios
        .post('/api/changeAvatar.php', formdata, config)
        .then((response) => {
          response = response.data;
          this.showInfo(response.info);
          this.loading = false;
        });
    },

    getUserAvatar() {
      axios.get('/api/getUserAvatar.php')
        .then((response) => {
          this.userAvatar = response.data.data.avatar;
          this.loaded = true;
        });
    },

    onChangeFileUpload() {
      [this.file] = this.$refs.file.files;
      this.url = URL.createObjectURL(this.file);
    },
  },

  mounted() {
    this.getUserAvatar();
  },
};
</script>

<style lang="scss" scoped>
.avatar{
  &__form{
    margin: 5px 0;
  }

  &__input{
    display: block;
    background: transparent;
    padding: 5px;
    font-size: 18px;
    margin: 10px 0;
    border: 3px dashed #ffbc00;
    color: var(--main-text-color);
    border-radius: 5px;
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
    margin-top: 5px;
    padding: 10px;
    font-size: 17px;
    background: #ffbc00;
    color: black;
    border-top-right-radius: 10px;
  }

  &__now{
    height: 50px;
    max-width: 100%;
    display: inline-block;
    height: 50px;
    width: 50px;
    border: 1px solid #bfbfbf;
    margin-right: 10px;
    border-radius: 100px;
    margin: 5px 5px 0 5px;
  }

  &__preview img{
    height: 50px;
    width: 50px;
    border: 1px solid #bfbfbf;
    border-radius: 100px;
    margin: 5px 5px 0 5px;
  }
}
</style>
