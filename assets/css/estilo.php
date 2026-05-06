<?php
/**
 * ============================================================
 * HOJA DE ESTILOS PERSONALIZADA
 * ============================================================
 * Nombre: assets/css/estilo.php
 * Propósito: CSS personalizado con tema corporate tech oscuro
 * Autor: Arquitecto Senior PHP/MySQL
 * Fecha: Mayo 2026
 * ============================================================
 */
header('Content-Type: text/css; charset=utf-8');
?>
/* ============================================================
   VARIABLES DE DISEÑO - TEMA CORPORATE TECH
   ============================================================ */
:root {
    --color-primary: #4f46e5;
    --color-tradicional: #ef4444;
    --color-startup: #10b981;
    --color-cio: #3b82f6;
    --color-cto: #8b5cf6;
    --color-ciso: #f59e0b;
    --sidebar-bg: #0f172a;
    --card-bg: #1e293b;
    --text-primary: #f1f5f9;
    --text-secondary: #cbd5e1;
    --border-color: #334155;
    --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ============================================================
   RESET Y TIPOGRAFÍA
   ============================================================ */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #0f172a 0%, #1a1f35 100%);
    color: var(--text-primary);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-size: 0.95rem;
    line-height: 1.6;
    min-height: 100vh;
}

a {
    color: var(--color-primary);
    text-decoration: none;
    transition: var(--transition-smooth);
}

a:hover {
    color: #6366f1;
}

/* ============================================================
   SIDEBAR LATERAL
   ============================================================ */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    height: 100vh;
    background-color: var(--sidebar-bg);
    border-right: 1px solid var(--border-color);
    overflow-y: auto;
    z-index: 1000;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 2rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 1.1rem;
    background: linear-gradient(135deg, var(--color-primary), #6366f1);
}

.user-info h5 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
}

.user-info p {
    margin: 0.25rem 0 0 0;
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.role-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    margin-top: 0.5rem;
}

.sidebar-nav {
    flex: 1;
    padding: 1.5rem 0;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.5rem;
    color: var(--text-secondary);
    border-left: 3px solid transparent;
    transition: var(--transition-smooth);
}

.sidebar-nav a:hover {
    background-color: rgba(79, 70, 229, 0.1);
    color: var(--color-primary);
    border-left-color: var(--color-primary);
}

.sidebar-nav a.active {
    background-color: rgba(79, 70, 229, 0.15);
    color: var(--color-primary);
    border-left-color: var(--color-primary);
    font-weight: 600;
}

.sidebar-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.sidebar-footer a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #94a3b8;
    font-size: 0.9rem;
}

/* ============================================================
   CONTENEDOR PRINCIPAL
   ============================================================ */
.main-content {
    margin-left: 260px;
    padding: 2rem;
    min-height: 100vh;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: var(--text-secondary);
}

/* ============================================================
   TARJETAS DE ESTADÍSTICAS
   ============================================================ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, var(--card-bg), #2d3748);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    transition: var(--transition-smooth);
}

.stat-card:hover {
    border-color: var(--color-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
}

.stat-label {
    font-size: 0.85rem;
    color: var(--text-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.stat-subtext {
    font-size: 0.8rem;
    color: #64748b;
}

/* ============================================================
   TARJETAS DE PROYECTOS
   ============================================================ */
.projects-section h2 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.project-card {
    background: var(--card-bg);
    border-radius: 0.75rem;
    border-left: 4px solid;
    overflow: hidden;
    transition: var(--transition-smooth);
    display: flex;
    flex-direction: column;
}

.project-card.tradicional {
    border-left-color: var(--color-tradicional);
}

.project-card.startup {
    border-left-color: var(--color-startup);
}

.project-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
}

.project-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.project-title {
    flex: 1;
}

.project-title h3 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.model-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
}

.model-badge.tradicional {
    background-color: rgba(239, 68, 68, 0.15);
    color: var(--color-tradicional);
    border: 1px solid var(--color-tradicional);
}

.model-badge.startup {
    background-color: rgba(16, 185, 129, 0.15);
    color: var(--color-startup);
    border: 1px solid var(--color-startup);
}

.state-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    min-width: 100px;
}

.state-badge.warning {
    background-color: rgba(245, 158, 11, 0.15);
    color: var(--color-ciso);
    border: 1px solid var(--color-ciso);
}

.state-badge.info {
    background-color: rgba(59, 130, 246, 0.15);
    color: var(--color-cio);
    border: 1px solid var(--color-cio);
}

.state-badge.primary {
    background-color: rgba(139, 92, 246, 0.15);
    color: var(--color-cto);
    border: 1px solid var(--color-cto);
}

.state-badge.success {
    background-color: rgba(16, 185, 129, 0.15);
    color: var(--color-startup);
    border: 1px solid var(--color-startup);
}

/* Pipeline de aprobación visual */
.pipeline-progress {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.pipeline-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.5rem;
}

.pipeline-step {
    flex: 1;
    text-align: center;
    position: relative;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    background-color: #334155;
    color: var(--text-secondary);
    border: 2px solid var(--border-color);
}

.pipeline-step.active .step-circle {
    background: linear-gradient(135deg, var(--color-primary), #6366f1);
    border-color: var(--color-primary);
    color: white;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.step-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    max-width: 80px;
    margin: 0 auto;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.project-content {
    padding: 1.5rem;
    flex: 1;
}

.project-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.project-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
    color: var(--text-secondary);
}

.project-meta {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

/* ============================================================
   FORMULARIOS Y BOTONES
   ============================================================ */
.form-control {
    background-color: #334155;
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    border-radius: 0.5rem;
    padding: 0.75rem;
    transition: var(--transition-smooth);
}

.form-control:focus {
    background-color: #475569;
    border-color: var(--color-primary);
    color: var(--text-primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-label {
    color: var(--text-primary);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: var(--transition-smooth);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    font-size: 0.95rem;
}

.btn-primary {
    background-color: var(--color-primary);
    color: white;
}

.btn-primary:hover {
    background-color: #6366f1;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.btn-success {
    background-color: var(--color-startup);
    color: white;
}

.btn-success:hover {
    background-color: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-warning {
    background-color: var(--color-ciso);
    color: white;
}

.btn-warning:hover {
    background-color: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.btn-danger {
    background-color: #dc2626;
    color: white;
}

.btn-danger:hover {
    background-color: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.btn-secondary {
    background-color: #475569;
    color: var(--text-primary);
}

.btn-secondary:hover {
    background-color: #64748b;
    transform: translateY(-1px);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}

/* ============================================================
   TABLAS (AUDITORÍA)
   ============================================================ */
.table {
    width: 100%;
    border-collapse: collapse;
    background-color: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    overflow: hidden;
}

.table thead {
    background-color: #334155;
    border-bottom: 1px solid var(--border-color);
}

.table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.95rem;
}

.table tbody tr:hover {
    background-color: rgba(79, 70, 229, 0.05);
}

.table-row-dev {
    background-color: rgba(107, 114, 128, 0.05);
}

.table-row-cio {
    background-color: rgba(59, 130, 246, 0.05);
}

.table-row-cto {
    background-color: rgba(139, 92, 246, 0.05);
}

.table-row-ciso {
    background-color: rgba(245, 158, 11, 0.05);
}

/* ============================================================
   MODALES Y ALERTAS
   ============================================================ */
.modal-content {
    background-color: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
}

.modal-header {
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid;
}

.alert-warning {
    background-color: rgba(245, 158, 11, 0.15);
    border-left-color: var(--color-ciso);
    color: var(--color-ciso);
}

.alert-success {
    background-color: rgba(16, 185, 129, 0.15);
    border-left-color: var(--color-startup);
    color: var(--color-startup);
}

.alert-danger {
    background-color: rgba(239, 68, 68, 0.15);
    border-left-color: var(--color-tradicional);
    color: var(--color-tradicional);
}

.alert-info {
    background-color: rgba(59, 130, 246, 0.15);
    border-left-color: var(--color-cio);
    color: var(--color-cio);
}

/* ============================================================
   PANTALLA DE LOGIN
   ============================================================ */
.login-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 1rem;
}

.login-box {
    width: 100%;
    max-width: 400px;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    padding: 2.5rem;
    box-shadow: 0 20px 25px rgba(0, 0, 0, 0.3);
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.logo-circle {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, var(--color-primary), #6366f1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
}

.login-header h1 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.login-header p {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
    }

    .main-content {
        margin-left: 200px;
        padding: 1rem;
    }

    .projects-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .pipeline-steps {
        flex-wrap: wrap;
    }
}

@media (max-width: 576px) {
    .sidebar {
        position: absolute;
        width: 100%;
        height: auto;
        margin-bottom: 2rem;
    }

    .main-content {
        margin-left: 0;
        padding: 1rem 0.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .page-header h1 {
        font-size: 1.5rem;
    }
}
