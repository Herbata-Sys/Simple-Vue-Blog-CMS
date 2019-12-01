<template>
  <div class="password" :style="this[theme]">
    <h2><font-awesome-icon icon="key"/> Zmień hasło</h2>

    <Loading v-if="loading" />

    <form v-if="!loading" class="password__form" v-on:submit.prevent="changePassword">
      <input type="password" class="password__input" placeholder="Stare hasło" minlength="6" v-model="passwordData.old" required>
      <input type="password" class="password__input" placeholder="Nowe hasło" minlength="6" v-model="passwordData.new" required>
      <input type="password" class="password__input" placeholder="Powtórz hasło" minlength="6" v-model="passwordData.newRepeat" required>
      <input type="submit" class="password__submit" value="Zmień hasło">
    </form>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';
import Loading from '@/components/Loading.vue';

export default {
  name: 'changePassword',

  components: {
    Loading,
  },

  data() {
    return {
      loading: false,
      passwordData: {
        old: '',
        new: '',
        newRepeat: '',
      },
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

    changePassword() {
      this.loading = true;
      const formdata = new FormData();
      formdata.append('old', this.passwordData.old);
      formdata.append('new', this.passwordData.new);
      formdata.append('newRepeat', this.passwordData.newRepeat);
      const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

      axios
        .post('/api/changePassword.php', formdata, config)
        .then((response) => {
          response = response.data;
          if (response.success) {
            this.passwordData.old = '';
            this.passwordData.new = '';
            this.passwordData.newRepeat = '';
          }
          this.showInfo(response.info);
          this.loading = false;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.password{
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
}
</style>
