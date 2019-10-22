<template>
  <div :style="this[theme]" class="info">
    {{ $store.state.infoMessage }}
  </div>
</template>

<script>
export default {
  name: 'Info',

  data() {
    return {
      lightCss: {
        '--info-background': '#000000e5',
      },
      darkCss: {
        '--info-background': '#000000e5',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },
};
</script>

<style scoped lang="scss">
.info{
  background: var(--info-background);
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  text-align: center;
  z-index: 10000;
  padding: 20px 10px;
  width: 100%;
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  color: white;
  min-height: 40px;
  font-size: 18px;
  font-weight: 600;
}
</style>
