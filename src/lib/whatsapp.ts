const PHONE = import.meta.env.PUBLIC_WA_PHONE || '5493794740207';

export function waLink(text: string): string {
  const encodedText = encodeURIComponent(text);
  return `https://wa.me/${PHONE}?text=${encodedText}`;
}

export function createPropertyMessage(property: { title: string; id: string }): string {
  return `Hola, me interesa la propiedad "${property.title}" (ID: ${property.id}). ¿Está disponible?`;
}

export function createSearchMessage(formData: {
  operation?: string;
  city?: string;
  type?: string;
  priceMin?: string;
  priceMax?: string;
  m2Min?: string;
  m2Max?: string;
  bedrooms?: string;
  bathrooms?: string;
  neighborhood?: string;
  message?: string;
}): string {
  let message = 'Hola, estoy buscando una propiedad con las siguientes características:\n\n';
  
  if (formData.operation) message += `• Operación: ${formData.operation}\n`;
  if (formData.city) message += `• Ciudad: ${formData.city}\n`;
  if (formData.type) message += `• Tipo: ${formData.type}\n`;
  if (formData.neighborhood) message += `• Barrio: ${formData.neighborhood}\n`;
  if (formData.bedrooms) message += `• Dormitorios: ${formData.bedrooms}\n`;
  if (formData.bathrooms) message += `• Baños: ${formData.bathrooms}\n`;
  if (formData.priceMin || formData.priceMax) {
    message += `• Precio: `;
    if (formData.priceMin && formData.priceMax) {
      message += `entre $${formData.priceMin} y $${formData.priceMax}\n`;
    } else if (formData.priceMin) {
      message += `desde $${formData.priceMin}\n`;
    } else if (formData.priceMax) {
      message += `hasta $${formData.priceMax}\n`;
    }
  }
  if (formData.m2Min || formData.m2Max) {
    message += `• Superficie: `;
    if (formData.m2Min && formData.m2Max) {
      message += `entre ${formData.m2Min}m² y ${formData.m2Max}m²\n`;
    } else if (formData.m2Min) {
      message += `desde ${formData.m2Min}m²\n`;
    } else if (formData.m2Max) {
      message += `hasta ${formData.m2Max}m²\n`;
    }
  }
  if (formData.message) {
    message += `\nMensaje adicional: ${formData.message}`;
  }
  
  return message;
}

export function createPublishMessage(formData: {
  address?: string;
  city?: string;
  type?: string;
  m2?: string;
  bedrooms?: string;
  bathrooms?: string;
  price?: string;
  condition?: string;
  videoLink?: string;
  message?: string;
}): string {
  let message = 'Hola, quiero publicar mi propiedad con las siguientes características:\n\n';
  
  if (formData.address) message += `• Dirección: ${formData.address}\n`;
  if (formData.city) message += `• Ciudad: ${formData.city}\n`;
  if (formData.type) message += `• Tipo: ${formData.type}\n`;
  if (formData.m2) message += `• Superficie: ${formData.m2}m²\n`;
  if (formData.bedrooms) message += `• Dormitorios: ${formData.bedrooms}\n`;
  if (formData.bathrooms) message += `• Baños: ${formData.bathrooms}\n`;
  if (formData.price) message += `• Precio deseado: $${formData.price}\n`;
  if (formData.condition) message += `• Estado: ${formData.condition}\n`;
  if (formData.videoLink) message += `• Link a video/fotos: ${formData.videoLink}\n`;
  if (formData.message) {
    message += `\nInformación adicional: ${formData.message}`;
  }
  
  return message;
}
