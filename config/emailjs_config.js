/**
 * Configuración de EmailJS para GameOn Network
 * 
 * INSTRUCCIONES DE CONFIGURACIÓN:
 * 
 * 1. Crear cuenta en EmailJS (https://www.emailjs.com/)
 * 2. Configurar un servicio de email (Gmail, Outlook, etc.)
 * 3. Crear un template de email
 * 4. Obtener las credenciales y reemplazar los valores abajo
 * 5. Incluir este archivo en las páginas que usen EmailJS
 */

// Configuración principal de EmailJS
const EMAILJS_CONFIG = {
    // Public Key de tu cuenta EmailJS
    // Reemplaza 'TU_PUBLIC_KEY_AQUI' con tu nueva Public Key de EmailJS.
    publicKey: 'dzLw38Sb7Z_XzxzzT',
    
    // Service ID configurado en EmailJS
    // Reemplaza 'TU_SERVICE_ID_AQUI' con tu nuevo Service ID.
    serviceID: 'service_gameon1',
    
    // Template ID para recuperación de contraseña
    // Reemplaza 'TU_TEMPLATE_ID_AQUI' con tu nuevo Template ID.
    templateID: 'template_owsx6wo'
};

/**
 * TEMPLATE SUGERIDO PARA EMAILJS:
 * 
 * Subject: Recuperación de Contraseña - GameOn Network
 * 
 * Body:
 * Hola {{to_name}},
 * 
 * Hemos recibido una solicitud para restablecer la contraseña de tu cuenta de {{user_type}} en GameOn Network.
 * 
 * Si solicitaste este cambio, haz clic en el siguiente enlace para crear una nueva contraseña:
 * 
 * {{recovery_link}}
 * 
 * Este enlace expirará el {{expiration_time}}.
 * 
 * Si no solicitaste este cambio, puedes ignorar este email. Tu contraseña permanecerá sin cambios.
 * 
 * Por tu seguridad:
 * - Nunca compartas este enlace con nadie
 * - El enlace solo funciona una vez
 * - Si tienes problemas, contacta nuestro soporte
 * 
 * Saludos,
 * Equipo GameOn Network
 * 
 * ---
 * Este es un email automático, por favor no respondas a este mensaje.
 */

/**
 * VARIABLES DEL TEMPLATE:
 * 
 * {{to_email}}         - Email del destinatario
 * {{to_name}}          - Nombre del usuario
 * {{recovery_link}}    - Enlace completo de recuperación
 * {{expiration_time}}  - Fecha y hora de expiración
 * {{user_type}}        - Tipo de usuario (Deportista/Institución Deportiva)
 */

// Función para inicializar EmailJS
function initEmailJS() {
    if (typeof emailjs !== 'undefined') {
        emailjs.init({
            publicKey: EMAILJS_CONFIG.publicKey,
        });
        console.log('EmailJS inicializado correctamente');
    } else {
        console.error('EmailJS SDK no está cargado');
    }
}

// Función para enviar email de recuperación
async function enviarEmailRecuperacion(emailData) {
    try {
        // Validar configuración
        if (EMAILJS_CONFIG.publicKey === 'TU_PUBLIC_KEY_AQUI' || 
            EMAILJS_CONFIG.serviceID === 'TU_SERVICE_ID_AQUI' || 
            EMAILJS_CONFIG.templateID === 'TU_TEMPLATE_ID_AQUI') {
            throw new Error('EmailJS no está configurado. Por favor, actualiza las credenciales en emailjs_config.js');
        }

        const templateParams = {
            to_email: emailData.to_email,
            to_name: emailData.to_name,
            recovery_link: emailData.recovery_link,
            expiration_time: emailData.expiration_time,
            user_type: emailData.user_type_display
        };

        console.log('Enviando email con parámetros:', templateParams);

        const response = await emailjs.send(
            EMAILJS_CONFIG.serviceID,
            EMAILJS_CONFIG.templateID,
            templateParams
        );

        console.log('Email enviado exitosamente:', response);
        return { success: true, response };
    } catch (error) {
        console.error('Error al enviar email:', error);
        return { success: false, error: error.message || error };
    }
}

// Inicializar cuando se carga el script
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEmailJS);
} else {
    initEmailJS();
}