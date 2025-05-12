import {defineStore} from 'pinia';
import {fetchWithoutBody, fetchWithBody} from '~/utils/utils';
import type {Commentaire} from "~/types/Commentaire";

export const useCommentaireStore = defineStore('commentaireStore', {
    state: () => ({
        // Liste des commentaires
        listCommentaire: [] as Commentaire[],
        loading : false,
        error : null,

        // Liste des sélects pour le formulaires commentaires
        listSelectCommentaire: [],
        loadingSelect : false,
        errorSelect : null,

        // Update Check Traite
        loadingUpdateTraite : false,
        errorUpdateTraite : null,
        displayBandeauTraite : false,
        colorBandeauTraite : '',
        descriptionBandeauTraite : '',

        // Add Commentaire
        loadingAddCommentaire : false,
        errorAddCommentaire : null,
        displayBandeauAddCommentaire : false,
        colorBandeauAddCommentaire : '',
        descriptionBandeauAddCommentaire : '',

        // AbortController
        controller: new AbortController(),
    }),
    actions: {

        abortPreviousRequest() {
            if (this.controller) {
                this.controller.abort();
            }
            this.controller = new AbortController();
        },

        async getCommentaire(dossierId: number,type : number | null = null) {

            this.loading = true
            this.abortPreviousRequest();

            try {
                this.listCommentaire = await fetchWithoutBody(`dossiers/${dossierId}/commentaires/${type ? type : 0}`, 'GET', this.controller.signal) as never[]
            } catch (e : any) {
                this.error = e.message
            } finally {
                this.loading = false
            }
        },

        async addCommentaire(commentaire : Commentaire, callBack: Function) {
            this.loadingAddCommentaire = true

            try {
                await fetchWithBody('addCommentaire', 'POST', commentaire)
                this.colorBandeauAddCommentaire =  'green'
                this.descriptionBandeauAddCommentaire = 'Commentaire ajouté avec succès.';
            } catch (e : any) {
                this.errorAddCommentaire = e.message
                this.colorBandeauAddCommentaire =  'red'
                this.descriptionBandeauAddCommentaire = 'Une erreur est survenue lors de l\'ajout du commentaire.';
            } finally {
                this.loadingAddCommentaire = false
                this.displayBandeauAddCommentaire = true
                this.errorAddCommentaire = null
            }

            setTimeout(() => {
                this.displayBandeauAddCommentaire = false
                if (this.errorAddCommentaire === null){
                    callBack();
                }

            }, 1500)
        },


        async updateCheckCommentaire(commentaireId : number, check : any) {
            this.loadingUpdateTraite = true
            try {
                this.colorBandeauTraite =  'orange'
                this.descriptionBandeauTraite = 'Mise à jour du commentaire en cours';
                this.displayBandeauTraite = true

                await fetchWithoutBody(`commentaires/${commentaireId}/checkCommentaire/${check}`, 'GET')

                this.colorBandeauTraite = 'green'
                this.descriptionBandeauTraite = 'Commentaire mise à jour avec succès.';
            } catch (e : any) {
                this.errorUpdateTraite = e.message
                this.colorBandeauTraite = 'red'
                this.descriptionBandeauTraite = 'Une erreur est survenue lors de la mise à jour du commentaire.';
            } finally {
                this.loadingUpdateTraite = false
                this.displayBandeauTraite = true
                this.errorUpdateTraite = null
            }
            if(this.colorBandeauTraite != 'orange') {
                setTimeout(() => {
                    this.displayBandeauTraite = false
                }, 2000)
            }
        },

        async getSelectCommentaire(){
            this.loadingSelect = true
            try {
                this.listSelectCommentaire = await fetchWithoutBody('commentaireForm', 'GET') as never[]
            } catch (e : any) {
                this.errorSelect = e.message
            } finally {
                this.loadingSelect = false
            }
        }

    },
});
