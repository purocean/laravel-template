import Url from './Url';

export default {
  previewImage(current, urls) {
    current = Url.fixFull(current);
    urls = urls.map(src => Url.fixFull(src));

    window.wx.previewImage({current, urls});
  },

  hideOptionMenu() {
    window.wx.hideOptionMenu();
  }
}
