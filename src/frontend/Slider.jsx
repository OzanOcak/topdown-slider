import { createRoot, useRef, useEffect, useState } from "@wordpress/element";
import "./index.jsx";
import { contrastColor } from "./utils/contrast_color";

export default function Slider({ slides }) {
  const trackRef = useRef(null);
  const [current, setCurrent] = useState(-1);
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  const [isDesktop, setIsDesktop] = useState(
    typeof window !== "undefined" ? window.innerWidth > 1024 : true,
  );

  // Track which slide is in view
  useEffect(() => {
    const root = trackRef.current;
    if (!root) return;
    const items = root.querySelectorAll(".fss-slide");
    if (!items.length) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            setCurrent(parseInt(entry.target.dataset.index, 10));
          }
        });
      },
      { root, threshold: 0.6 },
    );

    items.forEach((s) => observer.observe(s));
    return () => observer.disconnect();
  }, []);

  // Keyboard navigation
  useEffect(() => {
    const root = trackRef.current;
    if (!root) return;

    const onKey = (e) => {
      const items = root.querySelectorAll(".fss-slide");
      if (e.key === "ArrowDown" && current < items.length - 1) {
        items[current + 1].scrollIntoView({ behavior: "smooth" });
      }
      if (e.key === "ArrowUp" && current > 0) {
        items[current - 1].scrollIntoView({ behavior: "smooth" });
      }
    };

    root.addEventListener("keydown", onKey);
    return () => root.removeEventListener("keydown", onKey);
  }, [current]);

  useEffect(() => {
    setScrolled(current > 0);
  }, [current]);

  useEffect(() => {
    document.body.classList.toggle("fss-menu-open", menuOpen);
    return () => document.body.classList.remove("fss-menu-open");
  }, [menuOpen]);

  useEffect(() => {
    const onResize = () => setIsDesktop(window.innerWidth > 1024);
    window.addEventListener("resize", onResize);
    return () => window.removeEventListener("resize", onResize);
  }, []);

  const goTo = (idx) => {
    const items = trackRef.current?.querySelectorAll(".fss-slide");
    items?.[idx]?.scrollIntoView({ behavior: "smooth" });
  };

  const navItems = slides
    .map((slide, i) => ({ label: slide.label, index: i }))
    .filter((item) => item.label && item.label.trim());

  return (
    <>
      {navItems.length > 0 && (
        <button
          className="fss-menu-toggle"
          onClick={() => setMenuOpen(true)}
          aria-label="Open menu"
        >
          <span></span>
          <span></span>
          <span></span>
        </button>
      )}

      {navItems.length > 0 && (
        <nav className={`fss-nav${scrolled ? " is-scrolled" : ""}`}>
          <a
            href="#"
            className="fss-nav__logo"
            onClick={(e) => {
              e.preventDefault();
              goTo(0);
            }}
          >
            {slides[0]?.title || "Home"}
          </a>
          <ul className="fss-nav__links">
            {navItems.map((item) => (
              <li key={item.index}>
                <a
                  href={`#slide-${item.index}`}
                  onClick={(e) => {
                    e.preventDefault();
                    goTo(item.index);
                  }}
                >
                  {item.label}
                </a>
              </li>
            ))}
          </ul>
        </nav>
      )}
      {menuOpen && (
        <button
          className="fss-menu-overlay"
          onClick={() => setMenuOpen(false)}
          aria-label="Close menu"
        />
      )}

      <nav className={`fss-menu-panel${menuOpen ? " is-open" : ""}`}>
        <button
          className="fss-menu-panel__close"
          onClick={() => setMenuOpen(false)}
          aria-label="Close menu"
        >
          ×
        </button>
        <ul className="fss-menu-panel__links">
          {navItems.map((item) => (
            <li key={item.index}>
              <a
                href={`#slide-${item.index}`}
                className={current === item.index ? "is-active" : ""}
                onClick={(e) => {
                  e.preventDefault();
                  goTo(item.index);
                  setMenuOpen(false);
                }}
              >
                {item.label}
              </a>
            </li>
          ))}
        </ul>
      </nav>

      <div className="fss-slider" ref={trackRef} tabIndex={0}>
        {slides.map((slide, i) => (
          <section
            key={slide.id || i}
            id={`slide-${i}`}
            className={`fss-slide${i === current ? " is-active" : ""}`}
            data-index={i}
            data-position={slide.textPosition || "center"}
            data-text={slide.textAnimation || "fade-up"}
            data-image={slide.imageAnimation || "zoom"}
          >
            {slide.imageUrl && (
              <div
                className="fss-slide__bg"
                style={{ backgroundImage: `url(${slide.imageUrl})` }}
              />
            )}
            <div className="fss-slide__content">
              {slide.title && (
                <h2 className="fss-slide__title">{slide.title}</h2>
              )}
              {slide.description && (
                <p className="fss-slide__desc">{slide.description}</p>
              )}

              {slide.buttonText && slide.buttonText &&  (
                <a
                  href={slide.buttonUrl || "#"}
                  className={`fss-slide__button${
                    slide.buttonColor ? " fss-slide__button--solid" : ""
                  }`}
                  style={
                    slide.buttonColor
                      ? {
                          background: slide.buttonColor,
                          color: contrastColor(slide.buttonColor),
                          borderColor: slide.buttonColor,
                        }
                      : undefined
                  }
                >
                  {slide.buttonText}
                </a>
              )}
            </div>
          </section>
        ))}
      </div>

      {isDesktop && (
        <nav className="fss-slider__dots">
          {slides.map((_, i) => (
            <button
              key={i}
              className={`fss-slider__dot${i === current ? " is-active" : ""}`}
              onClick={() => goTo(i)}
              aria-label={`Go to slide ${i + 1}`}
            />
          ))}
        </nav>
      )}
    </>
  );
}

document.querySelectorAll(".fss-root").forEach((root) => {
  const id = root.id.replace("fss-root-", "");
  const dataEl = document.getElementById(`fss-data-${id}`);
  if (!dataEl) return;

  let data;
  try {
    data = JSON.parse(dataEl.textContent);
  } catch (e) {
    console.error("FSS: bad JSON", e);
    return;
  }

  createRoot(root).render(<Slider slides={data.slides || []} />);
});
