Simple PHP GD bar's

Can generate basic bars and output to base64 string.

Sample:

```
$barBase64 = (new Bar(
    width: 900,
    height: 60,
    barColor: BAR::COLOUR_GREEN,
    value: -6,
    unit: 'mm',
    boundaries: [8, 6, 4, 2],
    boundaryLabels: ['6', '4'],
))->getB64Image();
```

<img width="925" height="873" alt="image" src="https://github.com/user-attachments/assets/6b6de39b-f19e-4aee-a33a-f1f25f390c3f" />
