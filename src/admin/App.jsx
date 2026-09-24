import { useState, useEffect } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import SlideList from "./components/SlideList";
import SlideEditor from "./components/SlideEditor";

const { restUrl, nonce, sliderId: initialSliderId } = window.TDS;

export default function App() {
  const [sliderId, setSliderId] = useState(initialSliderId || null);
  const [title, setTitle] = useState("");
  const [slides, setSlides] = useState([]);
  const [currentIndex, setCurrentIndex] = useState(0);
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
    const newSlide = {
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
      buttonLinkMode: "page",
      textPosition: "center",
      textAnimation: "fade-up",
      imageAnimation: "zoom",
    };
    const next = [...slides, newSlide];
    setSlides(next);
    setCurrentIndex(next.length - 1);
  };

  const updateSlide = (id, patch) => {
    setSlides(slides.map((s) => (s.id === id ? { ...s, ...patch } : s)));
  };

  const removeSlide = (id) => {
    const next = slides.filter((s) => s.id !== id);
    setSlides(next);
    if (currentIndex >= next.length) {
      setCurrentIndex(Math.max(0, next.length - 1));
    }
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

  const currentSlide = slides[currentIndex];

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
            <code>{`[oocak_slider id="${sliderId}"]`}</code>
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

      <div className="tds-editor-layout">
        <SlideList
          slides={slides}
          currentIndex={currentIndex}
          onSelect={setCurrentIndex}
          onAdd={addSlide}
        />

        {currentSlide ? (
          <SlideEditor
            index={currentIndex}
            slide={currentSlide}
            onChange={(patch) => updateSlide(currentSlide.id, patch)}
            onRemove={() => removeSlide(currentSlide.id)}
          />
        ) : (
          <div className="tds-editor-empty">
            {__(
              "No slides yet. Click + Add to create one.",
              "topdown-slider",
            )}
          </div>
        )}
      </div>
    </div>
  );
}