(() => {
  const converter = new showdown.Converter();
  const input = document.querySelector('#Post_content');
  const viewer = document.querySelector('.markdown-viewer');
  const tabEl = document.querySelector('#nav-preview-tab');
  tabEl.addEventListener('shown.bs.tab', event => {
    const html = converter.makeHtml(input.value);
    viewer.innerHTML = html;
    hljs.highlightAll();
  });
})();

