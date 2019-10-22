module.exports = {
  devServer: {
    proxy: {
      "/api": {
        target: "http://localhost",
        secure: false
      },
      "/img": {
        target: "http://localhost",
        secure: false
      }
    }
  }
};