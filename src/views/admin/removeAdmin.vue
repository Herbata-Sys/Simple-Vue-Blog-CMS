<template>
  <div class="removeadmin" :style="this[theme]">
    <h2><font-awesome-icon icon="times"/> Odbierz uprawnienia administracyjne</h2>

    <div class="removeadmin__container">
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

export default {
  name: 'removeAdmin',

  data() {
    return {
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
    ...mapActions([
      'showInfo',
    ]),

    removeAdmin() {
      axios.get('/api/removeAdmin.php', {
        params: {
          id: this.$route.params.id,
        },
      })
        .then((response) => {
          response = response.data;
          if (response.success) this.$router.push('/admin/users');
          this.showInfo(response.info);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.removeadmin{

  &__button{
    cursor: pointer;
    display: block;
    background: transparent;
    padding: 5px;
    margin: 10px 0;
    border: 1px solid #ffbc00;
    font-weight: 600;
    font-size: 20px;
    color: var(--main-text-color);
  }

  &__text{
    font-size: 20px;
    padding: 40px 0 20px 0;
  }
}
</style>
