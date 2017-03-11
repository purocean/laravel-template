import Http from './Http';

export default {
  previewImage(current, urls) {
    current = Http.fixFullUrl(current);
    urls = urls.map(src => Http.fixFullUrl(src));

    window.wx.previewImage({current, urls});
  },

  hideOptionMenu() {
    window.wx.hideOptionMenu();
  }
}
