import slugify from './slugify';

describe('slugify', () => {
  it('Should properly slugify example strings.', () => {
    expect(slugify('My sample string!')).toEqual('my-sample-string');
  });

  it('Should return slugified strings as-is.', () => {
    expect(slugify('my-sample-string')).toEqual('my-sample-string');
  });
});
