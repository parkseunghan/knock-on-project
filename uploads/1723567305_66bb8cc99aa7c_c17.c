#include <stdio.h>
#include <stdlib.h>



int main() {
	int* arr;
	int num;

	scanf("%d", &num);

	arr = (int *)calloc(num, sizeof(int));
	if (arr == NULL) {
		//실행문장
		return 1;
	}

	for (int i = 0; i < num; i++) {
		scanf("%d", &arr[i]);
	}

	for (int i = 0; i < num; i++) {
		printf("%d ", arr[i]);
	}
	printf("\n");

	int new_num;
	scanf("%d", &new_num);

	arr = (int *)realloc(num * sizeof(int));
	if (arr == NULL) {
		//실행문장
		return 1;
	}

	for (int i = new_num; i < new_num; i++) {
		scanf("%d", &arr[i]);
	}

	for (int i = 0; i < new_num; i++) {
		printf("%d ", arr[i]);
	}
	printf("\n");

	free(arr);

	return 0;
}
