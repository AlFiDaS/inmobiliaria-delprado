import { useState, useEffect, useMemo } from 'react';
import type { Property } from '@/data/properties';

interface GalleryProps {
  property: Property;
}

// Función helper para cache busting de imágenes
function addCacheBust(imageUrl: string, timestamp?: number): string {
  if (!imageUrl || imageUrl === '/images/placeholder.jpg') return imageUrl;
  const separator = imageUrl.includes('?') ? '&' : '?';
  const cacheParam = timestamp || Math.floor(Date.now() / 1000);
  return `${imageUrl}${separator}v=${cacheParam}`;
}

export default function Gallery({ property }: GalleryProps) {
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const [activeTab, setActiveTab] = useState<'images' | 'videos'>('images');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const { images: originalImages, videos, listedAt } = property;
  
  // Agregar cache busting a las imágenes usando timestamp de listedAt
  const images = useMemo(() => {
    if (!originalImages || originalImages.length === 0) return [];
    let timestamp: number | undefined = undefined;
    if (listedAt) {
      const date = new Date(listedAt);
      timestamp = Math.floor(date.getTime() / 1000);
    }
    return originalImages.map(img => addCacheBust(img, timestamp));
  }, [originalImages, listedAt]);

  const hasVideos = videos && videos.length > 0;

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        goToPrevious();
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        goToNext();
      } else if (e.key === 'Escape' && isModalOpen) {
        e.preventDefault();
        setIsModalOpen(false);
      }
    };

    document.addEventListener('keydown', handleKeyDown);
    return () => document.removeEventListener('keydown', handleKeyDown);
  }, [isModalOpen]);

  const goToPrevious = () => {
    setCurrentImageIndex(prev => 
      prev === 0 ? images.length - 1 : prev - 1
    );
  };

  const goToNext = () => {
    setCurrentImageIndex(prev => 
      prev === images.length - 1 ? 0 : prev + 1
    );
  };

  const goToImage = (index: number) => {
    setCurrentImageIndex(index);
  };

  const openModal = () => {
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
  };

  const renderVideo = (video: { kind: 'file' | 'youtube' | 'vimeo'; src: string }) => {
    if (video.kind === 'youtube') {
      const videoId = video.src.split('/').pop()?.split('?')[0];
      return (
        <iframe
          src={`https://www.youtube.com/embed/${videoId}`}
          title="Video de la propiedad"
          className="w-full h-full"
          allowFullScreen
        />
      );
    } else if (video.kind === 'vimeo') {
      const videoId = video.src.split('/').pop();
      return (
        <iframe
          src={`https://player.vimeo.com/video/${videoId}`}
          title="Video de la propiedad"
          className="w-full h-full"
          allowFullScreen
        />
      );
    } else {
      return (
        <video
          src={video.src}
          controls
          className="w-full h-full"
          preload="metadata"
        >
          Tu navegador no soporta el elemento de video.
        </video>
      );
    }
  };

  return (
    <div className="gallery-container w-full max-w-full overflow-hidden mt-2.5" style={{boxSizing: 'border-box'}}>
      {/* Tabs */}
      {hasVideos && (
        <div className="flex mb-4">
          <button
            onClick={() => setActiveTab('images')}
            className={`px-4 py-2 text-sm font-medium rounded-l-md border ${
              activeTab === 'images'
                ? 'bg-orange-600 text-white border-orange-600'
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
            }`}
          >
            Imágenes ({images.length})
          </button>
          <button
            onClick={() => setActiveTab('videos')}
            className={`px-4 py-2 text-sm font-medium rounded-r-md border-t border-r border-b ${
              activeTab === 'videos'
                ? 'bg-orange-600 text-white border-orange-600'
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
            }`}
          >
            Videos ({videos.length})
          </button>
        </div>
      )}

      {/* Contenido principal */}
      <div className="relative bg-gray-100 rounded-lg overflow-hidden w-4/5 aspect-square mx-auto" style={{maxWidth: '80%', width: '80%'}}>
        {activeTab === 'images' ? (
          <>
            {/* Imagen principal */}
            <img
              src={images[currentImageIndex]}
              alt={`${property.title} - Imagen ${currentImageIndex + 1}`}
              className="w-full h-full object-cover cursor-pointer hover:opacity-90 transition-opacity"
              style={{maxWidth: '100%', maxHeight: '100%', objectFit: 'cover', objectPosition: 'center'}}
              onClick={openModal}
            />

            {/* Navegación */}
            {images.length > 1 && (
              <>
                <button
                  onClick={goToPrevious}
                  className="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity"
                  aria-label="Imagen anterior"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
                <button
                  onClick={goToNext}
                  className="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity"
                  aria-label="Imagen siguiente"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </>
            )}

            {/* Contador */}
            {images.length > 1 && (
              <div className="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-sm">
                {currentImageIndex + 1} / {images.length}
              </div>
            )}
          </>
        ) : (
          /* Videos */
          <div className="w-full h-full">
            {videos && videos[0] && renderVideo(videos[0])}
          </div>
        )}
      </div>

      {/* Thumbnails */}
      {activeTab === 'images' && images.length > 1 && (
        <div className="mt-4 flex space-x-2 overflow-x-auto scrollbar-hide" ref={(el) => {
          if (el) {
            // Scroll automático para mantener la miniatura activa visible
            const activeThumbnail = el.children[currentImageIndex] as HTMLElement;
            if (activeThumbnail) {
              activeThumbnail.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
              });
            }
          }
        }}>
          {images.map((image, index) => (
            <button
              key={index}
              onClick={() => goToImage(index)}
              className={`gallery-thumbnail relative flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all duration-200 ${
                index === currentImageIndex
                  ? 'border-orange-500 ring-2 ring-orange-200 shadow-lg'
                  : 'border-gray-200 hover:border-gray-300'
              }`}
            >
              <img
                src={image}
                alt={`Miniatura ${index + 1}`}
                className={`w-full h-full object-cover transition-all duration-200 ${
                  index === currentImageIndex
                    ? 'opacity-100'
                    : 'opacity-70 hover:opacity-90'
                }`}
              />
              {/* Indicador de imagen activa */}
              {index === currentImageIndex && (
                <div className="absolute inset-0 bg-orange-500 bg-opacity-20 flex items-center justify-center">
                  <div className="w-2 h-2 bg-orange-500 rounded-full"></div>
                </div>
              )}
            </button>
          ))}
        </div>
      )}

      {/* Instrucciones de teclado */}
      {activeTab === 'images' && images.length > 1 && (
        <p className="mt-2 text-xs text-gray-500 text-center">
          Usa las flechas ← → del teclado para navegar
        </p>
      )}

      {/* Modal de pantalla completa */}
      {isModalOpen && (
        <div className="fixed inset-0 z-[9999] bg-black bg-opacity-90 flex items-center justify-center">
          <div className="relative w-full h-full flex items-center justify-center p-4">
            {/* Botón cerrar */}
            <button
              onClick={closeModal}
              className="absolute top-4 right-4 z-[10000] bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity"
              aria-label="Cerrar modal"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            {/* Imagen en pantalla completa */}
            <img
              src={images[currentImageIndex]}
              alt={`${property.title} - Imagen ${currentImageIndex + 1}`}
              className="max-w-full max-h-full object-contain"
            />

            {/* Navegación en modal */}
            {images.length > 1 && (
              <>
                <button
                  onClick={goToPrevious}
                  className="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition-opacity"
                  aria-label="Imagen anterior"
                >
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
                <button
                  onClick={goToNext}
                  className="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition-opacity"
                  aria-label="Imagen siguiente"
                >
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </>
            )}

            {/* Contador en modal */}
            {images.length > 1 && (
              <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full text-lg">
                {currentImageIndex + 1} / {images.length}
              </div>
            )}

            {/* Instrucciones en modal */}
            <div className="absolute top-4 left-4 bg-black bg-opacity-50 text-white px-3 py-2 rounded-lg text-sm">
              Usa ← → para navegar, ESC para cerrar
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
