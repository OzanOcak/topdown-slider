import { createRoot } from "@wordpress/element";
import Slider from "./Slider";
import "./styles/base.css";
import "./styles/content.css";
import "./styles/positions.css";
import "./styles/animations.css";
import "./styles/nav-desktop.css";
import "./styles/nav-mobile.css";
import "./styles/responsive.css";
import "./styles/accessibility.css";

document.querySelectorAll(".tds-root").forEach((root) => {
  const id = root.id.replace("tds-slider-", "");
  const dataEl = document.getElementById(`tds-data-${id}`);
  if (!dataEl) return;

  let data;
  try {
    data = JSON.parse(dataEl.textContent);
  } catch (e) {
    console.error("TDS: bad JSON", e);
    return;
  }

  createRoot(root).render(<Slider slides={data.slides || []} />);
});