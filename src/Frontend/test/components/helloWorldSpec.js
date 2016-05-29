import React from 'react';
import HelloWorld from '../../components/HelloWorld';
import { expect } from 'chai';
import { shallow } from 'enzyme';

describe('helloWorld', () => {
  it('renders hello world', () => {
    const result = shallow(<HelloWorld />);
    expect(result.prop('component')).to.equal('h1');
    expect(result.prop('content')).to.equal('pages.hello.head');
  });
});
