import { __ } from "@wordpress/i18n";

export default function SlideCard({ index, slide, onChange, onRemove }) {
  const pickImage = () => {
    const frame = window.wp.media({
      title: __("Choose image", "topdown-slider"),
      multiple: false,
      library: { type: "image" },
    });

    frame.on("select", () => {
      const att = frame.state().get("selection").first().toJSON();
      onChange({
        imageId: att.id,
        imageUrl: att.sizes?.large?.url || att.url,
      });
    });

    frame.open();
  };

  return (
    <div className="tds-card">
      <div className="tds-card__header">
        <span className="tds-card__index">{index + 1}</span>
        <button className="tds-btn tds-btn--danger" onClick={onRemove}>
          {__("Remove", "topdown-slider")}
        </button>
      </div>

      <div
        className="tds-card__preview"
        onClick={pickImage}
        style={{
          backgroundImage: slide.imageUrl ? `url(${slide.imageUrl})` : "none",
        }}
      >
        {!slide.imageUrl && (
          <span className="tds-card__preview-empty">
            {__("Click to choose image", "topdown-slider")}
          </span>
        )}
        {slide.imageUrl && (
          <div className="tds-card__preview-overlay">
            <h3>{slide.title || __("Slide title", "topdown-slider")}</h3>
            <p>{slide.description || __("Description…", "topdown-slider")}</p>
          </div>
        )}
      </div>

      <div className="tds-card__fields">
        <label>
          <span>{__("Nav label", "topdown-slider")}</span>
          <input
            type="text"
            value={slide.label || ""}
            onChange={(e) => onChange({ label: e.target.value })}
            placeholder={__("e.g. Home", "topdown-slider")}
          />
        </label>
        <label>
          <span>{__("Title", "topdown-slider")}</span>
          <input
            type="text"
            value={slide.title}
            onChange={(e) => onChange({ title: e.target.value })}
          />
        </label>
        <label>
          <span>{__("Description", "topdown-slider")}</span>
          <textarea
            rows={3}
            value={slide.description}
            onChange={(e) => onChange({ description: e.target.value })}
          />
        </label>
        
        <label className="tds-toggle-row">
          <input
            type="checkbox"
            checked={!!slide.buttonEnabled}
            onChange={(e) => onChange({ buttonEnabled: e.target.checked })}
          />
          <span>{__("Show a button on this slide", "topdown-slider")}</span>
        </label>

        {slide.buttonEnabled && (
  <>
    <label>
      <span>{__("Button text", "topdown-slider")}</span>
      <input
        type="text"
        value={slide.buttonText || ""}
        onChange={(e) => onChange({ buttonText: e.target.value })}
        placeholder={__("e.g. Learn more", "topdown-slider")}
      />
    </label>

    <label>
      <span>{__("Button link type", "topdown-slider")}</span>
      <div className="tds-radio-row">
        <label>
          <input
            type="radio"
            name={`link-type-${slide.id}`}
            checked={(slide.buttonLinkMode || "page") === "page"}
            onChange={() => onChange({ buttonLinkMode: "page" })}
          />
          <span>{__("Page", "topdown-slider")}</span>
        </label>
        <label>
          <input
            type="radio"
            name={`link-type-${slide.id}`}
            checked={slide.buttonLinkMode === "custom"}
            onChange={() => onChange({ buttonLinkMode: "custom" })}
          />
          <span>{__("Custom URL", "topdown-slider")}</span>
        </label>
      </div>
    </label>

    {(slide.buttonLinkMode || "page") === "page" ? (
      <label>
        <span>{__("Select page", "topdown-slider")}</span>
        <select
          value={slide.buttonUrl || ""}
          onChange={(e) => onChange({ buttonUrl: e.target.value })}
        >
          <option value="">{__("— Choose a page —", "topdown-slider")}</option>
          {(window.TDS.pages || []).map((page) => (
            <option key={page.id} value={page.link}>
              {page.title}
            </option>
          ))}
        </select>
      </label>
    ) : (
      <label>
        <span>{__("Custom URL", "topdown-slider")}</span>
        <input
          type="text"
          value={slide.buttonUrl || ""}
          onChange={(e) => onChange({ buttonUrl: e.target.value })}
          placeholder="https://example.com"
        />
      </label>
    )}

    <label>
      <span>{__("Button color", "topdown-slider")}</span>
      <input
        type="color"
        value={slide.buttonColor || "#ffffff"}
        onChange={(e) => onChange({ buttonColor: e.target.value })}
        style={{ height: "40px", padding: "2px" }}
      />
      {slide.buttonColor && (
        <button
          type="button"
          className="tds-btn tds-btn--danger"
          style={{ marginTop: "6px", fontSize: "11px", padding: "4px 8px" }}
          onClick={() => onChange({ buttonColor: "" })}
        >
          {__("Reset to default", "topdown-slider")}
        </button>
      )}
    </label>
  </>
)}


        <label>
          <span>{__("Text position", "topdown-slider")}</span>
          <select
            value={slide.textPosition || "center"}
            onChange={(e) => onChange({ textPosition: e.target.value })}
          >
            <option value="top-left">Top left</option>
            <option value="top-right">Top right</option>
            <option value="center">Center</option>
            <option value="bottom-left">Bottom left</option>
            <option value="bottom-right">Bottom right</option>
          </select>
        </label>

        <label>
          <span>{__("Text animation", "topdown-slider")}</span>
          <select
            value={slide.textAnimation || "fade-up"}
            onChange={(e) => onChange({ textAnimation: e.target.value })}
          >
            <option value="fade-up">{__("Fade up", "topdown-slider")}</option>
            <option value="fade-down">{__("Fade down", "topdown-slider")}</option>
            <option value="slide-left">
              {__("Slide from left", "topdown-slider")}
            </option>
            <option value="slide-right">
              {__("Slide from right", "topdown-slider")}
            </option>
            <option value="zoom-in">{__("Zoom in", "topdown-slider")}</option>
            <option value="none">{__("None (static)", "topdown-slider")}</option>
          </select>
        </label>

        <label>
          <span>{__("Image animation", "topdown-slider")}</span>
          <select
            value={slide.imageAnimation || "zoom"}
            onChange={(e) => onChange({ imageAnimation: e.target.value })}
          >
            <option value="zoom">{__("Slow zoom", "topdown-slider")}</option>
            <option value="pan-left">{__("Pan left", "topdown-slider")}</option>
            <option value="pan-right">
              {__("Pan right", "topdown-slider")}
            </option>
            <option value="none">{__("None (static)", "topdown-slider")}</option>
          </select>
        </label>
      </div>
    </div>
  );
}