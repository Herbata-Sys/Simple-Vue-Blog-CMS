<template>
  <div class="menu" :style="this[theme]">
    <div class="menu__dropdown" @click="menuDropdown" data-show="true">
      <div class="menu__title">Menu</div>
      <font-awesome-icon class="menu__icon icon--down" icon="angle-down"/>
      <font-awesome-icon class="menu__icon icon--up" icon="angle-up"/>
    </div>
    <ul class="menu__list">
      <li class="menu__item"><router-link to="/user" class="menu__link"><font-awesome-icon class="menu__icon" icon="user"/> Panel użytkownika</router-link></li>
      <li class="menu__item"><router-link to="/user/changePassword" class="menu__link"><font-awesome-icon class="menu__icon" icon="key"/> Zmień hasło</router-link></li>
      <li class="menu__item"><router-link to="/user/changeAvatar" class="menu__link"><font-awesome-icon class="menu__icon" icon="file-image"/> Zmień avatar</router-link></li>
      <li class="menu__item"><router-link to="/user/myComments" class="menu__link"><font-awesome-icon class="menu__icon" icon="comment-alt"/> Moje komentarze</router-link></li>
    </ul>

    <div v-if="this.$store.state.user.admin==='1'" class="menu__admin">
      <div class="menu__dropdown" @click="menuDropdown" data-show="true">
        <div class="menu__title">Administrator</div>
        <font-awesome-icon class="menu__icon icon--down" icon="angle-down"/>
        <font-awesome-icon class="menu__icon icon--up" icon="angle-up"/>
      </div>
      <ul class="menu__list">
        <li class="menu__item"><router-link to="/admin/blog" class="menu__link"><font-awesome-icon class="menu__icon" icon="columns"/> Blog</router-link></li>
        <li class="menu__item"><router-link to="/admin/addArticle" class="menu__link"><font-awesome-icon class="menu__icon" icon="plus-square"/> Dodaj artykuł</router-link></li>
        <li class="menu__item"><router-link to="/admin/articles" class="menu__link"><font-awesome-icon class="menu__icon" icon="newspaper"/> Artykuły</router-link></li>
        <li class="menu__item"><router-link to="/admin/users" class="menu__link"><font-awesome-icon class="menu__icon" icon="users"/> Użytkownicy</router-link></li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserMenu',

  data() {
    return {
      lightCss: {
        '--main-text-color': 'black',
        '--hover-text-color': 'white',
        '--hover-menu-background': '#213477',
        '--boxshadow': '#b1b1b1',
        '--dropdown-border': '#c7c7c7',
      },
      darkCss: {
        '--main-text-color': 'white',
        '--hover-text-color': 'black',
        '--hover-menu-background': '#424242',
        '--boxshadow': 'black',
        '--dropdown-border': '#c7c7c7',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  methods: {
    menuDropdown(e) {
      if (e.currentTarget.dataset.show === 'true') {
        e.currentTarget.nextSibling.style.display = 'none';
        e.currentTarget.querySelector('.icon--down').style.display = 'none';
        e.currentTarget.querySelector('.icon--up').style.display = 'block';
      } else {
        e.currentTarget.nextSibling.style.display = 'block';
        e.currentTarget.querySelector('.icon--up').style.display = 'none';
        e.currentTarget.querySelector('.icon--down').style.display = 'block';
      }

      e.currentTarget.dataset.show = e.currentTarget.dataset.show === 'true' ? 'false' : 'true';
    },
  },

  mounted() {
    if (JSON.parse(localStorage.user).logged !== true) this.$router.push({ path: '/' });
  },
};
</script>

<style scoped lang="scss">
.menu{
  width: 230px;
  box-shadow: 0px 0px 20px -10px var(--boxshadow);
  z-index: 2;
  height: fit-content;
  color: var(--main-text-color);

  &__list{
    list-style: none;
    padding: 0;
    margin: 0;
    background: var(--menu-background);
  }

  &__item{
    color: var(--main-text-color);
    background: var(--hover-text-color);
    cursor: pointer;

    &:hover{
      background: var(--hover-menu-background);
    }

    &:hover a{
      color: var(--hover-text-color);
    }
  }

  &__icon{
    margin-right: 5px;
  }

  &__link{
    padding: 10px 20px;
    text-decoration: none;
    display: block;
    color: var(--main-text-color);
    font-weight: 600;

    &.router-link-exact-active{
      background: var(--hover-menu-background);
      color: var(--hover-text-color);
    }
  }

  &__dropdown{
    display: flex;
    justify-content: space-between;
    padding: 10px;
    cursor: pointer;
    border-top: 1px dashed var(--dropdown-border);
  }
}

.icon{
  &--up{
    display: none;
  }
}
</style>
