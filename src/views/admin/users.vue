<template>
  <div class="users" :style="this[theme]">
    <h2><font-awesome-icon icon="users"/> Użytkownicy</h2>

    <div v-if="!loading" class="users__container">
      <table class="users__table">
        <tr>
          <th @click="sortBy('id')" title="Sortuj po ID">ID</th>
          <th @click="sortBy('name')" title="Sortuj po Autorze">Nick</th>
          <th @click="sortBy('email')" title="Sortuj po Emailu">Email</th>
          <th @click="sortBy('id')" title="Sortuj po dacie rejestracji">Data rejestracji</th>
          <th @click="sortBy('admin')" title="Czy użytkownik posiada uprawnienia administracyjne?">Administrator</th>
          <th>Akcje</th>
        </tr>
        <tr v-for="user in users" :key="user.id">
          <td class="users__id">#{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td class="users__date">{{ user.reg_date }}</td>
          <td :class="user.admin === '1' ? 'users__admin' : 'users__nonAdmin'">{{ user.admin === "1" ? "Tak" : "Nie"}}</td>
          <td>
            <div><router-link :to="'deleteUser/'+user.id" class="users__delete">Usuń</router-link></div>
            <router-link :to="user.admin === '1' ? 'removeAdmin/'+user.id : 'addAdmin/'+user.id" class="users__edit">Admin{{ user.admin === '1' ? '-' : '+' }}</router-link>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'users',

  data() {
    return {
      loading: 1,
      sorting: {
        id: 'desc',
      },
      users: [],
      lightCss: {
        '--main-text-color': 'black',
        '--tr-background': '#f2f2f2',
        '--td-border': '#e7e7e7',
        '--tr-hover': '#d4d6ff',
      },
      darkCss: {
        '--main-text-color': '#d8d8d8',
        '--tr-background': '#f2f2f212',
        '--td-border': '#525252',
        '--tr-hover': '#00000047',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  methods: {
    getUsers() {
      axios.get('/api/usersGetAll.php')
        .then((response) => {
          this.users = response.data.data;
          this.loading = 0;
        });
    },

    sortBy(by) {
      if (this.sorting[by] === 'desc') {
        this.users.sort((a, b) => {
          if (by === 'id') {
            a[by] = parseInt(a[by], 10);
            b[by] = parseInt(b[by], 10);
          }

          a[by] = a[by];
          b[by] = b[by];
          if (a[by] < b[by]) return -1;
          if (a[by] > b[by]) return 1;
          return 0;
        });
        this.sorting[by] = 'asc';
      } else {
        this.users.sort((a, b) => {
          if (by === 'id') {
            a[by] = parseInt(a[by], 10);
            b[by] = parseInt(b[by], 10);
          }

          a[by] = a[by];
          b[by] = b[by];
          if (a[by] > b[by]) return -1;
          if (a[by] < b[by]) return 1;
          return 0;
        });
        this.sorting[by] = 'desc';
      }
    },
  },

  mounted() {
    this.getUsers();
  },
};
</script>

<style lang="scss" scoped>
.users{

  &__container{
    animation: rolldown .7s;
    overflow: hidden;
  }

  &__table{
    margin: 20px 0;
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;

    a{
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: 1s transform;

      &:hover{
        transform: translateY(-3px)
      }
    }

    th, td {
      text-align: center;
      white-space: pre;
      padding: 15px 10px;
      vertical-align: middle;
    }

    tr:nth-child(even){
      background-color: var(--tr-background);
      box-shadow: 1px 1px 20px -10px black;

      td{
        border-right: 1px solid var(--td-border);
        border-left: 1px solid var(--td-border);
      }
    }

    tr:not(:first-child){
      transition: .3s background-color;
      will-change: background-color;

      &:hover{
        background-color: var(--tr-hover);
      }
    }

    tr:first-child{

      th:first-child{
        border-top-left-radius: 10px;
      }

      th:last-child{
        border-top-right-radius: 10px;
      }
    }

    th{
      text-align: center;
      color: white;
      background: #213477;
      cursor: pointer;
    }

    tr td:nth-of-type(4){
      white-space: unset;
      word-break: break-word;
      text-overflow: ellipsis;
      text-align: center;
    }

  }

  &__tableText{
    max-height: 200px;
    overflow-y: auto;
  }

  &__delete{
    color: #ff0000;
  }

  &__edit{
    color: #0059ff;
  }

  &__id{
    color: #ff6100;
  }

  &__date{
    font-size: 12px;
  }

  &__admin{
    background: linear-gradient(100deg, rgba(255,255,255,0) 10%, rgba(1,255,0,0.2) 100%);
    font-weight: 600;
  }

  &__nonAdmin{
    background: linear-gradient(100deg, rgba(255,255,255,0) 10%, rgba(255, 0, 0, 0.2) 100%);
    font-weight: 600;
  }
}

@keyframes rolldown {
  0% {
    height: 0;
    opacity: 0;
    transform: scaleY(0);
    transform-origin: 0 0
  }
  100% {
    height: 500px;
    opacity: 1;
    transform: scaleY(1);
    transform-origin: 0 0
  }
}
</style>
