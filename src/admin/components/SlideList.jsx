import { __ } from "@wordpress/i18n";

export default function SlideList({ slides, currentIndex, onSelect, onAdd }) {
  return (
    <aside className="tds-slide-list">
      <div className="tds-slide-list__header">
        {__("Slides", "topdown-slider")}
      </div>

      <ul className="tds-slide-list__items">
        {slides.map((slide, i) => (
          <li key={slide.id}>
            <button
              type="button"
              className={`tds-slide-list__item${
                i === currentIndex ? " is-active" : ""
              }`}
              onClick={() => onSelect(i)}
            >
              <span className="tds-slide-list__index">{i + 1}</span>
              <span
                className="tds-slide-list__thumb"
                style={
                  slide.imageUrl
                    ? { backgroundImage: `url(${slide.imageUrl})` }
                    : undefined
                }
              />
              <span className="tds-slide-list__title">
                {slide.title || slide.label || __("Untitled", "topdown-slider")}
              </span>
            </button>
          </li>
        ))}
      </ul>

      <button
        type="button"
        className="tds-slide-list__add"
        onClick={onAdd}
      >
        {__("+ Add Slide", "topdown-slider")}
      </button>
    </aside>
  );
}
