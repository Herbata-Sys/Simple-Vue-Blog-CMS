<template>
  <div class="removeadmin" :style="this[theme]">
    <h2><font-awesome-icon icon="times"/> Odbierz uprawnienia administracyjne</h2>

    <Loading v-if="loading" />

    <div v-if="!loading" class="removeadmin__container">
      <div class="removeadmin__text">
        Odebrać uprawnienia administracyjne użytkownikowi o id: <b>{{ $route.params.id }}</b>? Straci on możliwość zarządzania stroną.
      </div>
      <input type="button" value="Odbierz" class="removeadmin__button" @click="removeAdmin">
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';
import Loading from '@/components/Loading.vue';

export default {
  name: 'removeAdmin',

  components: {
    Loading,
  },

  data() {
    return {
      loading: false,
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

    removeAdmin() {
      this.loading = true;
      axios.get('/api/removeAdmin.php', {
        params: {
          id: this.$route.params.id,
        },
      })
        .then((response) => {
          response = response.data;
          if (response.success) this.$router.push('/admin/users');
          this.showInfo(response.info);
          this.loading = false;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.removeadmin{

  &__button{
    transition: background .5s;
    will-change: background;
    cursor: pointer;
    display: block;
    background: transparent;
    padding: 5px 10px;
    margin: 10px auto;
    border: 1px solid #ffbc00;
    font-weight: 600;
    font-size: 20px;
    color: var(--main-text-color);

    &:hover{
      background: var(--button-hover-color);
    }
  }

  &__text{
    text-align: center;
    font-size: 20px;
    padding: 40px 0 20px 0;
  }
}
</style>
