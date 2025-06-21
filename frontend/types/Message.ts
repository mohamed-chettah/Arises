import type {Slot} from "vue";

export interface Message {
    id: string; role: 'user' | 'assistant';
    content: string,
    slots ?: Slot[]
}
