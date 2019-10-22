<template>
  <div class="addadmin" :style="this[theme]">
    <h2><font-awesome-icon icon="plus-square"/> Przyznaj uprawnienia administracyjne</h2>

    <div class="addadmin__container">
      <div class="addadmin__text">
        Przyznać uprawnienia administracyjne użytkownikowi o id: <b>{{ $route.params.id }}</b>? Będzie on posiadał wszystkie uprawnienia do zarządzania stroną.
      </div>
      <input type="button" value="Przyznaj" class="addadmin__button" @click="addAdmin">
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';

export default {
  name: 'addadmin',

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

    addAdmin() {
      axios.get('/api/addAdmin.php', {
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
.addadmin{

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
