<template>
  <div class="user" :style="this[theme]">
    <div class="user__container">
      <UserMenu />

      <div class="user__content">
        <div class="content__container">
          <router-view></router-view>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import UserMenu from '@/components/UserMenu.vue';

export default {
  name: 'user',

  components: {
    UserMenu,
  },

  data() {
    return {
      lightCss: {
        '--main-text-color': 'black',
        '--container-background': '#f5f7fb',
        '--content-background': 'white',
        '--box-shadow': '#b1b1b1',
      },
      darkCss: {
        '--main-text-color': '#d8d8d8',
        '--container-background': 'black',
        '--content-background': '#1d1d1d',
        '--box-shadow': 'black',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  mounted() {
    document.title = 'Panel użytkownika';
  },
};
</script>

<style lang="scss" scoped>
.user{

  &__container{
    display: flex;
    margin-bottom: 50px;
  }

  &__content{
    padding: 0 0 0 20px;
    background: var(--container-background);
    color: var(--main-text-color);
    width: calc(100% - 230px);
  }
}

.content{

  &__container{
    background: var(--content-background);
    color: var(--main-text-color);
    min-height: 50vh;
    padding: 10px 20px;
    box-shadow: 0px 0px 20px -10px var(--box-shadow);
  }
}

@media only screen and (max-width: 800px) {
  .user{

    &__container{
      display: block;
    }

    &__content{
      width: 100%;
      padding: 20px 0;
    }
  }

  .menu{
    width: 100%;
  }
}


</style>
<style scoped>
.user__content >>> h2{
  font-size: 18px;
  font-weight: 900;
  padding-bottom: 10px;
  border-bottom: 1px solid #d4d4d4
}

.user__content >>> p{
  margin: 20px 0;
  font-size: 18px;
}
</style>
