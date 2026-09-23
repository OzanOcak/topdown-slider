import { createRoot } from "@wordpress/element";
import App from "./App";
import "./styles/admin.css";

const root = document.getElementById("tds-root");
if (root) {
  // Load the pages list from the inline JSON script tag.
  const pagesEl = document.getElementById("tds-pages-data");
  if (pagesEl && window.TDS) {
    try {
      window.TDS.pages = JSON.parse(pagesEl.textContent);
    } catch (e) {
      window.TDS.pages = [];
    }
  }

  const sliderId = parseInt(root.dataset.sliderId, 10) || 0;
  createRoot(root).render(<App sliderId={sliderId} />);
}