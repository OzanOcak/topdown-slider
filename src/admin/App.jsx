import { useState, useEffect } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import SlideCard from "./components/SlideCard";

const { restUrl, nonce, sliderId: initialSliderId } = window.TDS;

export default function App() {
  const [sliderId, setSliderId] = useState(initialSliderId || null);
  const [title, setTitle] = useState("");
  const [slides, setSlides] = useState([]);
  const [saving, setSaving] = useState(false);
  const [notice, setNotice] = useState(null);

  useEffect(() => {
    if (!sliderId) return;
    (async () => {
      const data = await fetch(`${restUrl}/slider/${sliderId}`, {
        headers: { "X-WP-Nonce": nonce },
      }).then((r) => r.json());

      if (data && !data.code) {
        setTitle(data.title || "");
        setSlides(Array.isArray(data.slides) ? data.slides : []);
      }
    })();
  }, [sliderId]);

  const addSlide = () => {
    setSlides([
      ...slides,
      {
        id: `s_${Date.now()}`,
        imageId: 0,
        imageUrl: "",
        label: "",
        title: "",
        description: "",
        buttonEnabled: false,
        buttonText: "",
        buttonUrl: "",
        buttonColor: "",
        textPosition: "center",
        textAnimation: "fade-up",
        imageAnimation: "zoom",
      },
    ]);
  };

  const updateSlide = (id, patch) => {
    setSlides(slides.map((s) => (s.id === id ? { ...s, ...patch } : s)));
  };

  const removeSlide = (id) => {
    setSlides(slides.filter((s) => s.id !== id));
  };

  const save = async () => {
    setSaving(true);
    setNotice(null);
    const res = await fetch(`${restUrl}/slider/${sliderId}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": nonce,
      },
      body: JSON.stringify({ slides, title }),
    });
    setSaving(false);
    if (res.ok) {
      setNotice({ type: "success", text: __("Saved.", "topdown-slider") });
    } else {
      setNotice({ type: "error", text: __("Save failed.", "topdown-slider") });
    }
  };

  if (!sliderId) {
    return (
      <div className="tds-loading">
        {__("Loading…", "topdown-slider")}
      </div>
    );
  }

  return (
    <div className="tds-admin">
      <header className="tds-header">
        <input
          className="tds-title-input"
          value={title}
          onChange={(e) => setTitle(e.target.value)}
          placeholder={__("Slider name", "topdown-slider")}
        />
        <div className="tds-header-actions">
          <span className="tds-shortcode-hint">
            {__("Shortcode:", "topdown-slider")}{" "}
            <code>{`[topdown_slider id="${sliderId}"]`}</code>
          </span>
          <button
            className="tds-btn tds-btn--primary"
            onClick={save}
            disabled={saving}
          >
            {saving
              ? __("Saving…", "topdown-slider")
              : __("Save", "topdown-slider")}
          </button>
        </div>
      </header>

      {notice && (
        <div className={`tds-notice tds-notice--${notice.type}`}>
          {notice.text}
        </div>
      )}

      <div className="tds-slides">
        {slides.map((slide, i) => (
          <SlideCard
            key={slide.id}
            index={i}
            slide={slide}
            onChange={(patch) => updateSlide(slide.id, patch)}
            onRemove={() => removeSlide(slide.id)}
          />
        ))}
      </div>

      <button className="tds-btn tds-btn--add" onClick={addSlide}>
        {__("+ Add Slide", "topdown-slider")}
      </button>
    </div>
  );
}