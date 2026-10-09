<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DirecteurController;
use App\Http\Controllers\DirectriceAdjointeController;
use App\Http\Controllers\ComptableController;
use App\Http\Controllers\CaissierController;
use App\Http\Controllers\VendeurController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PlanComptableController;
use App\Http\Controllers\Admin\SystemeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

// ==================== ROUTES D'AUTHENTIFICATION ====================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==================== ROUTES PROTÉGÉES (AUTH) ====================
Route::middleware(['auth'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==================== ROUTES DIRECTEUR (DR) ====================
    Route::prefix('directeur')->middleware('role:DR')->name('directeur.')->group(function () {
        Route::get('/dashboard', [DirecteurController::class, 'dashboard'])->name('dashboard');
        Route::get('/statistiques', [DirecteurController::class, 'statistiques'])->name('statistiques');
        Route::get('/journal', [DirecteurController::class, 'journal'])->name('journal');
        Route::get('/grand-livre', [DirecteurController::class, 'grandLivre'])->name('grand-livre');
        Route::get('/balance', [DirecteurController::class, 'balance'])->name('balance');
        Route::get('/bilan', [DirecteurController::class, 'bilan'])->name('bilan');
        Route::get('/resultat', [DirecteurController::class, 'resultat'])->name('resultat');
        Route::get('/banque', [DirecteurController::class, 'banque'])->name('banque');
        Route::get('/caisse', [DirecteurController::class, 'caisse'])->name('caisse');
        Route::get('/export/journal/pdf', [DirecteurController::class, 'exportJournalPDF'])->name('export.journal.pdf');
        Route::get('/export/balance/pdf', [DirecteurController::class, 'exportBalancePDF'])->name('export.balance.pdf');
        Route::get('/export/bilan/pdf', [DirecteurController::class, 'exportBilanPDF'])->name('export.bilan.pdf');
        Route::get('/export/resultat/pdf', [DirecteurController::class, 'exportResultatPDF'])->name('export.resultat.pdf');
        Route::get('/export/statistiques/pdf', [DirecteurController::class, 'exportStatistiquesPDF'])->name('export.statistiques.pdf');
        Route::get('/export/grand-livre/pdf', [DirecteurController::class, 'exportGrandLivrePDF'])->name('export.grand-livre.pdf'); // ⭐ AJOUTER CETTE LIGNE
        });

    // ==================== ROUTES DIRECTRICE ADJOINTE (DA) ====================
    Route::prefix('directrice')->middleware('role:DA')->name('directrice.')->group(function () {
        Route::get('/dashboard', [DirectriceAdjointeController::class, 'dashboard'])->name('dashboard');
        Route::get('/statistiques', [DirectriceAdjointeController::class, 'statistiques'])->name('statistiques');
        Route::get('/operations', [DirectriceAdjointeController::class, 'operationsAValider'])->name('operations');
        Route::post('/valider/{id}', [DirectriceAdjointeController::class, 'validerOperation'])->name('valider');
        Route::post('/rejeter/{id}', [DirectriceAdjointeController::class, 'rejeterOperation'])->name('rejeter');
        Route::get('/journal', [DirectriceAdjointeController::class, 'journal'])->name('journal');
        Route::get('/balance', [DirectriceAdjointeController::class, 'balance'])->name('balance');
        Route::get('/bilan', [DirectriceAdjointeController::class, 'bilan'])->name('bilan');
        Route::get('/resultat', [DirectriceAdjointeController::class, 'resultat'])->name('resultat');
        Route::get('/banque', [DirectriceAdjointeController::class, 'banque'])->name('banque');
        Route::get('/caisse', [DirectriceAdjointeController::class, 'caisse'])->name('caisse');
        Route::get('/export/journal/pdf', [DirectriceAdjointeController::class, 'exportJournalPDF'])->name('export.journal.pdf');
        Route::get('/export/balance/pdf', [DirectriceAdjointeController::class, 'exportBalancePDF'])->name('export.balance.pdf');
        Route::get('/export/bilan/pdf', [DirectriceAdjointeController::class, 'exportBilanPDF'])->name('export.bilan.pdf');
        Route::get('/export/resultat/pdf', [DirectriceAdjointeController::class, 'exportResultatPDF'])->name('export.resultat.pdf');
        Route::get('/export/statistiques/pdf', [DirectriceAdjointeController::class, 'exportStatistiquesPDF'])->name('export.statistiques.pdf');
    });

    // ==================== ROUTES COMPTABLE (CO) ====================
    Route::prefix('comptable')->middleware('role:CO')->name('comptable.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ComptableController::class, 'dashboard'])->name('dashboard');
        Route::get('/statistiques', [ComptableController::class, 'statistiques'])->name('statistiques');
        
        // Écritures
        Route::get('/ecritures', [ComptableController::class, 'ecritures'])->name('ecritures');
        Route::get('/ecritures/create', [ComptableController::class, 'createEcriture'])->name('ecritures.create');
        Route::post('/ecritures', [ComptableController::class, 'storeEcriture'])->name('ecritures.store');
        Route::get('/ecritures/{id}/edit', [ComptableController::class, 'editEcriture'])->name('ecritures.edit');
        Route::put('/ecritures/{id}', [ComptableController::class, 'updateEcriture'])->name('ecritures.update');
        Route::delete('/ecritures/{id}', [ComptableController::class, 'destroyEcriture'])->name('ecritures.destroy');
        Route::get('/ecritures/{id}', [ComptableController::class, 'showEcriture'])->name('ecritures.show');
        Route::get('/ecritures/{id}/valider', [ComptableController::class, 'validerEcriture'])->name('ecritures.valider');
        
        // Journal
        Route::get('/journal', [ComptableController::class, 'journal'])->name('journal');
        Route::get('/export/journal/pdf', [ComptableController::class, 'exportJournalPDF'])->name('export.journal.pdf');
        Route::get('/comptable/export/journal/excel', [ComptableController::class, 'exportJournalExcel'])->name('comptable.export.journal.excel');

        // Grand Livre
        Route::get('/grand-livre', [ComptableController::class, 'grandLivre'])->name('grand-livre');
        Route::get('/export/grand-livre/pdf', [ComptableController::class, 'exportGrandLivrePDF'])->name('export.grand-livre.pdf');
        Route::get('/comptable/export/grand-livre/excel', [ComptableController::class, 'exportGrandLivreExcel'])->name('comptable.export.grand-livre.excel');

        // Balance
        Route::get('/balance', [ComptableController::class, 'balance'])->name('balance');
        Route::get('/export/balance/pdf', [ComptableController::class, 'exportBalancePDF'])->name('export.balance.pdf');
        Route::get('/comptable/export/balance/excel', [ComptableController::class, 'exportBalanceExcel'])->name('comptable.export.balance.excel');

        // Bilan
        Route::get('/bilan', [ComptableController::class, 'bilan'])->name('bilan');
        Route::get('/export/bilan/pdf', [ComptableController::class, 'exportBilanPDF'])->name('export.bilan.pdf');
        Route::get('/comptable/export/bilan/excel', [ComptableController::class, 'exportBilanExcel'])->name('comptable.export.bilan.excel');

        // Compte de résultat
        Route::get('/resultat', [ComptableController::class, 'resultat'])->name('resultat');
        Route::get('/export/resultat/pdf', [ComptableController::class, 'exportResultatPDF'])->name('export.resultat.pdf');
        
      Route::get('/comptable/export/resultat/excel', [ComptableController::class, 'exportResultatExcel'])->name('comptable.export.resultat.excel');


        
        // Clients
        Route::get('/clients', [ComptableController::class, 'clients'])->name('clients');
        Route::get('/clients/create', [ComptableController::class, 'createClient'])->name('clients.create');
        Route::post('/clients', [ComptableController::class, 'storeClient'])->name('clients.store');
        Route::get('/clients/{id}/edit', [ComptableController::class, 'editClient'])->name('clients.edit');
        Route::put('/clients/{id}', [ComptableController::class, 'updateClient'])->name('clients.update');
        Route::delete('/clients/{id}', [ComptableController::class, 'destroyClient'])->name('clients.destroy');
        Route::get('/clients/{id}', [ComptableController::class, 'showClient'])->name('clients.show');
        
        // Fournisseurs
        Route::get('/fournisseurs', [ComptableController::class, 'fournisseurs'])->name('fournisseurs');
        Route::get('/fournisseurs/create', [ComptableController::class, 'createFournisseur'])->name('fournisseurs.create');
        Route::post('/fournisseurs', [ComptableController::class, 'storeFournisseur'])->name('fournisseurs.store');
        Route::get('/fournisseurs/{id}/edit', [ComptableController::class, 'editFournisseur'])->name('fournisseurs.edit');
        Route::put('/fournisseurs/{id}', [ComptableController::class, 'updateFournisseur'])->name('fournisseurs.update');
        Route::delete('/fournisseurs/{id}', [ComptableController::class, 'destroyFournisseur'])->name('fournisseurs.destroy');
        Route::get('/fournisseurs/{id}', [ComptableController::class, 'showFournisseur'])->name('fournisseurs.show');
        
        // Exports
        Route::get('/export/statistiques/pdf', [ComptableController::class, 'exportStatistiquesPDF'])->name('export.statistiques.pdf');
   
         // ==================== PLAN COMPTABLE POUR COMPTABLE ====================
   // Plan comptable pour comptable
  // Plan comptable pour comptable
    Route::prefix('plan-comptable')->group(function () {
        Route::get('/', [App\Http\Controllers\Comptable\PlanComptableController::class, 'index']);
        Route::get('/create', [App\Http\Controllers\Comptable\PlanComptableController::class, 'create']);
        Route::post('/', [App\Http\Controllers\Comptable\PlanComptableController::class, 'store']);
        Route::get('/{id}/edit', [App\Http\Controllers\Comptable\PlanComptableController::class, 'edit']);
        Route::put('/{id}', [App\Http\Controllers\Comptable\PlanComptableController::class, 'update']);
        Route::get('/{id}/delete', [App\Http\Controllers\Comptable\PlanComptableController::class, 'confirmDelete']);
        Route::delete('/{id}', [App\Http\Controllers\Comptable\PlanComptableController::class, 'destroy']);
        Route::get('/{id}', [App\Http\Controllers\Comptable\PlanComptableController::class, 'show']);
        });
   
   
        });

    // ==================== ROUTES CAISSIER (CA) ====================
    Route::prefix('caissier')->middleware('role:CA')->name('caissier.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [CaissierController::class, 'dashboard'])->name('dashboard');
        Route::get('/statistiques', [CaissierController::class, 'statistiques'])->name('statistiques');
        
        // Caisse
        Route::get('/caisse', [CaissierController::class, 'caisse'])->name('caisse');
        Route::get('/caisse/entree', [CaissierController::class, 'createEntree'])->name('caisse.entree');
        Route::post('/caisse/entree', [CaissierController::class, 'storeEntree'])->name('caisse.entree.store');
        Route::get('/caisse/sortie', [CaissierController::class, 'createSortie'])->name('caisse.sortie');
        Route::post('/caisse/sortie', [CaissierController::class, 'storeSortie'])->name('caisse.sortie.store');
        Route::delete('/caisse/{id}', [CaissierController::class, 'deleteOperation'])->name('caisse.delete');
        
        // Banque
        Route::get('/banque', [CaissierController::class, 'banque'])->name('banque');
        Route::get('/banque/depot', [CaissierController::class, 'createDepot'])->name('banque.depot');
        Route::post('/banque/depot', [CaissierController::class, 'storeDepot'])->name('banque.depot.store');
        Route::get('/banque/retrait', [CaissierController::class, 'createRetrait'])->name('banque.retrait');
        Route::post('/banque/retrait', [CaissierController::class, 'storeRetrait'])->name('banque.retrait.store');
        Route::delete('/banque/{id}', [CaissierController::class, 'deleteOperationBanque'])->name('banque.delete');
        
        // Rapports
        Route::get('/rapports/journalier', [CaissierController::class, 'rapportJournalier'])->name('rapports.journalier');
        Route::get('/rapports/journalier/pdf', [CaissierController::class, 'exportRapportJournalierPDF'])->name('rapports.journalier.pdf');
        Route::get('/rapports/mensuel', [CaissierController::class, 'rapportMensuel'])->name('rapports.mensuel');
        Route::get('/rapports/mensuel/pdf', [CaissierController::class, 'exportRapportMensuelPDF'])->name('rapports.mensuel.pdf');
        
        // Exports
        Route::get('/export/banque/pdf', [CaissierController::class, 'exportBanquePDF'])->name('export.banque.pdf');
        Route::get('/export/caisse/pdf', [CaissierController::class, 'exportCaissePDF'])->name('export.caisse.pdf');
        Route::get('/export/statistiques/pdf', [CaissierController::class, 'exportStatistiquesPDF'])->name('export.statistiques.pdf');
    });

    // ==================== ROUTES VENDEUR (VE) ====================
    Route::prefix('vendeur')->middleware('role:VE')->name('vendeur.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [VendeurController::class, 'dashboard'])->name('dashboard');
        Route::get('/statistiques', [VendeurController::class, 'statistiques'])->name('statistiques');
        
        // Factures
        Route::get('/factures', [VendeurController::class, 'factures'])->name('factures');
        Route::get('/factures/create', [VendeurController::class, 'createFacture'])->name('factures.create');
        Route::post('/factures', [VendeurController::class, 'storeFacture'])->name('factures.store');
        Route::get('/factures/{id}/edit', [VendeurController::class, 'editFacture'])->name('factures.edit');
        Route::put('/factures/{id}', [VendeurController::class, 'updateFacture'])->name('factures.update');
        Route::delete('/factures/{id}', [VendeurController::class, 'destroyFacture'])->name('factures.destroy');
        Route::get('/factures/{id}', [VendeurController::class, 'showFacture'])->name('factures.show');
        
        // Encaissements
        Route::get('/encaissements', [VendeurController::class, 'encaissements'])->name('encaissements');
        Route::post('/encaissements', [VendeurController::class, 'enregistrerEncaissement'])->name('encaissements.store');
        Route::get('/encaissements/historique', [VendeurController::class, 'historiqueEncaissements'])->name('encaissements.historique');
        
        // Ventes
        Route::get('/ventes', [VendeurController::class, 'ventes'])->name('ventes');
        Route::get('/export/ventes/pdf', [VendeurController::class, 'exportVentesPDF'])->name('export.ventes.pdf');
        Route::get('/export/ventes/excel', [VendeurController::class, 'exportVentesExcel'])->name('export.ventes.excel');
        Route::get('/export/encaissements/pdf', [VendeurController::class, 'exportEncaissementsPDF'])->name('export.encaissements.pdf');
        
        // Clients (vendeur)
        Route::get('/clients', [VendeurController::class, 'clientsList'])->name('clients');
        Route::get('/clients/create', [VendeurController::class, 'createClientVendeur'])->name('clients.create');
        Route::post('/clients', [VendeurController::class, 'storeClientVendeur'])->name('clients.store');
        
        // Exports
        Route::get('/export/statistiques/pdf', [VendeurController::class, 'exportStatistiquesPDF'])->name('export.statistiques.pdf');
    });

    // ==================== ROUTES ADMINISTRATEUR (AD) ====================
    Route::prefix('admin')->middleware('role:AD')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/statistiques', [AdminController::class, 'statistiques'])->name('statistiques');
        Route::get('/export/statistiques/pdf', [AdminController::class, 'exportStatistiquesPDF'])->name('export.statistiques.pdf');
        
        // Gestion utilisateurs
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::post('/users/{id}/toggle', [UserController::class, 'toggleActif'])->name('users.toggle');
        
        // Gestion logs
        Route::get('/logs', [LogController::class, 'index'])->name('logs');
        Route::get('/logs/{id}', [LogController::class, 'show'])->name('logs.show');
        Route::get('/logs/statistiques', [LogController::class, 'statistiques'])->name('logs.statistiques');
        Route::delete('/logs/clear', [LogController::class, 'clear'])->name('logs.clear');
        Route::delete('/logs/clear-old', [LogController::class, 'clearOld'])->name('logs.clear-old');
        Route::get('/logs/export', [LogController::class, 'export'])->name('logs.export');
        
        // Gestion permissions
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions');
        Route::get('/permissions/matrix', [PermissionController::class, 'permissionsMatrix'])->name('permissions.matrix');
        Route::get('/permissions/role/create', [PermissionController::class, 'createRole'])->name('permissions.create-role');
        Route::post('/permissions/role', [PermissionController::class, 'storeRole'])->name('permissions.store-role');
        Route::get('/permissions/role/{id}/edit', [PermissionController::class, 'editRole'])->name('permissions.edit-role');
        Route::put('/permissions/role/{id}', [PermissionController::class, 'updateRole'])->name('permissions.update-role');
        Route::delete('/permissions/role/{id}', [PermissionController::class, 'deleteRole'])->name('permissions.delete-role');
        Route::post('/permissions/assign', [PermissionController::class, 'assignRole'])->name('permissions.assign');
        
        // Plan comptable
       Route::prefix('plan-comptable')->group(function () {
        // Routes principales
        Route::get('/', [PlanComptableController::class, 'index'])->name('admin.plan-comptable.index');
        Route::get('/create', [PlanComptableController::class, 'create'])->name('admin.plan-comptable.create');
        Route::post('/', [PlanComptableController::class, 'store'])->name('admin.plan-comptable.store');
        Route::get('/{id}/edit', [PlanComptableController::class, 'edit'])->name('admin.plan-comptable.edit');
        Route::put('/{id}', [PlanComptableController::class, 'update'])->name('admin.plan-comptable.update');
        Route::delete('/{id}', [PlanComptableController::class, 'destroy'])->name('admin.plan-comptable.destroy');
        Route::get('/{id}', [PlanComptableController::class, 'show'])->name('admin.plan-comptable.show');
        Route::put('/{id}', [PlanComptableController::class, 'update'])->name('update');
           
    // ==================== ROUTES ÉCRITURES (AJOUTÉES) ====================
   Route::prefix('ecritures')->group(function () {
    Route::get('/', [ComptableController::class, 'ecritures'])->name('ecritures');
    Route::get('/create', [ComptableController::class, 'createEcriture'])->name('ecritures.create');
    Route::post('/', [ComptableController::class, 'storeEcriture'])->name('ecritures.store');
    Route::get('/{id}/edit', [ComptableController::class, 'editEcriture'])->name('ecritures.edit');
    Route::put('/{id}', [ComptableController::class, 'updateEcriture'])->name('ecritures.update');
    Route::delete('/{id}', [ComptableController::class, 'destroyEcriture'])->name('ecritures.destroy');
       // ⭐ AJOUTER CETTE LIGNE ⭐
        Route::get('/{id}/delete', [ComptableController::class, 'confirmDeleteEcriture'])->name('comptable.ecritures.confirm-delete');
    Route::get('/{id}', [ComptableController::class, 'showEcriture'])->name('ecritures.show');
    Route::get('/{id}/valider', [ComptableController::class, 'validerEcriture'])->name('ecritures.valider');


      // ⭐ AJOUTEZ CES DEUX ROUTES POUR LA SUPPRESSION ⭐
    Route::delete('/{id}', [ComptableController::class, 'destroyClient'])->name('destroy');
    Route::get('/{id}/delete', [ComptableController::class, 'confirmDeleteClient'])->name('confirm-delete');
    
        });

            // ROUTE POUR LA CONFIRMATION DE SUPPRESSION (GET)
            Route::get('/{id}/delete', [PlanComptableController::class, 'confirmDelete'])->name('confirm-delete');
            
            // ROUTE POUR LA SUPPRESSION (DELETE)
            Route::delete('/{id}', [PlanComptableController::class, 'destroy'])->name('destroy');   

        
        // Routes d'import/export - CRUCIALES
        // Routes d'import/export (utilise ImportController pour import)
    Route::get('/import', [PlanComptableController::class, 'importForm'])->name('plan-comptable.import');
        Route::post('/import', [PlanComptableController::class, 'import'])->name('plan-comptable.import.post');
        Route::get('/export', [PlanComptableController::class, 'export'])->name('plan-comptable.export');
        Route::get('/template', [PlanComptableController::class, 'template'])->name('plan-comptable.template');
        Route::post('/{id}/solde', [PlanComptableController::class, 'updateSolde'])->name('plan-comptable.update-solde');
        Route::get('/search', [PlanComptableController::class, 'search'])->name('plan-comptable.search');    
    // Système
        Route::get('/systeme', [SystemeController::class, 'index'])->name('systeme');
        Route::get('/systeme/info', [SystemeController::class, 'info'])->name('systeme.info');
        Route::get('/systeme/logs', [SystemeController::class, 'logs'])->name('systeme.logs');
        Route::get('/systeme/backups', [SystemeController::class, 'backups'])->name('systeme.backups');
        Route::get('/systeme/backup/download/{filename}', [SystemeController::class, 'downloadBackup'])->name('systeme.backup.download');
        Route::delete('/systeme/backup/delete/{filename}', [SystemeController::class, 'deleteBackup'])->name('systeme.backup.delete');
        Route::match(['get', 'post'], '/systeme/maintenance', [SystemeController::class, 'maintenance'])->name('systeme.maintenance');
        Route::post('/systeme/clear-cache', [SystemeController::class, 'clearCache'])->name('systeme.clear-cache');
        Route::post('/systeme/optimize', [SystemeController::class, 'optimize'])->name('systeme.optimize');
        Route::post('/systeme/backup', [SystemeController::class, 'backup'])->name('systeme.backup');
    });

    // ==================== ROUTES D'EXPORT ET IMPORT GÉNÉRAUX ====================
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/journal/pdf', [ExportController::class, 'journalPDF'])->name('journal.pdf');
        Route::get('/balance/pdf', [ExportController::class, 'balancePDF'])->name('balance.pdf');
        Route::get('/bilan/pdf', [ExportController::class, 'bilanPDF'])->name('bilan.pdf');
        Route::get('/resultat/pdf', [ExportController::class, 'resultatPDF'])->name('resultat.pdf');
        Route::get('/grand-livre/pdf', [ExportController::class, 'grandLivrePDF'])->name('grand-livre.pdf');
    });

    Route::prefix('import')->name('import.')->group(function () {
        Route::post('/plan-comptable', [ImportController::class, 'planComptable'])->name('plan-comptable');
        Route::post('/clients', [ImportController::class, 'clients'])->name('clients');
        Route::post('/fournisseurs', [ImportController::class, 'fournisseurs'])->name('fournisseurs');
    });

    // ==================== ROUTES DE RAPPORTS ====================
    Route::prefix('rapports')->name('rapports.')->group(function () {
        Route::get('/journalier', [RapportController::class, 'journalier'])->name('journalier');
        Route::get('/mensuel', [RapportController::class, 'mensuel'])->name('mensuel');
        Route::get('/trimestriel', [RapportController::class, 'trimestriel'])->name('trimestriel');
        Route::get('/annuel', [RapportController::class, 'annuel'])->name('annuel');
    });
});

});

require __DIR__.'/auth.php';
