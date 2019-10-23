module.exports = {
  devServer: {
    proxy: {
      "/api": {
        target: "http://localhost:80",
        secure: false,
      },

      "/img": {
        target: "http://localhost:80",
        secure: false,
      },
    },
  },
};